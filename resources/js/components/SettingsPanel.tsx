import React, { memo, useMemo, useCallback } from "react";
import { Trash2 } from "lucide-react";
import {
    BlockData,
    BlockInstance,
    BlockSchema,
    SectionData,
    SectionInstance,
    SettingSchema,
} from "@/types/page-builder";
import { useStore } from "@/core/store/useStore";
import { useEditorInstance } from "@/core/editorContext";
import { useEditorNavigation } from "@/hooks/useEditorNavigation";
import { useEditorLayout } from "@/hooks/useEditorLayout";
import SettingsHeader from "./settings/SettingsHeader";
import SettingsActions from "./settings/SettingsActions";
import FieldRenderer from "./settings/fields/FieldRenderer";

/**
 * Walk the section block tree following `path` and return the block instance
 * at the end of the path, or null if any step is missing.
 */
function resolveBlockByPath(
    section: SectionInstance | null,
    path: string[]
): BlockInstance | null {
    if (!section || path.length === 0) return null;
    let node: BlockInstance | undefined = section.blocks?.[path[0]];
    for (let i = 1; i < path.length; i++) {
        if (!node) return null;
        node = node.blocks?.[path[i]];
    }
    return node ?? null;
}

/**
 * Expand block type stubs (including @theme wildcards) into full BlockSchema objects.
 */
function expandBlockTypes(
    raw: Array<{ type: string; name?: string; settings?: any[] }>,
    themeBlocks: Record<string, BlockData>
): BlockSchema[] {
    const result: BlockSchema[] = [];
    for (const b of raw) {
        if (b.type === "@theme") {
            Object.values(themeBlocks).forEach((bd) => result.push(bd.schema));
        } else {
            const themeBlockSchema = themeBlocks[b.type]?.schema;
            const hasSettings = b.settings && b.settings.length > 0;
            const hasCustomName =
                themeBlockSchema &&
                b.name !== undefined &&
                b.name !== themeBlockSchema.name;

            // If it has custom settings or a custom name, it's explicitly defined in the section.
            // Otherwise, it's likely a standard reference that should pull the full theme block schema.
            if (hasSettings || hasCustomName || !themeBlockSchema) {
                result.push(b as BlockSchema);
            } else {
                result.push(themeBlockSchema);
            }
        }
    }
    return result;
}

function SettingsPanel() {
    const editor = useEditorInstance();
    const layout = useEditorLayout();
    const sections = useStore((s) => s.sections) as Record<string, SectionData>;
    const themeBlocks = useStore((s) => s.blocks) as Record<string, BlockData>;
    const currentPage = useStore((s) => s.currentPage);

    const {
        selectedSection,
        selectedBlock,
        blockPath,
        goBack,
    } = useEditorNavigation();

    const section = useMemo<SectionInstance | null>(() => {
        if (!selectedSection || !currentPage) return null;
        return currentPage.sections?.[selectedSection] ?? null;
    }, [selectedSection, currentPage]);

    const isLayoutSection = !!section?.layout;

    const handleUpdateSectionSettings = useCallback(
        (sectionId: string, patch: Record<string, any>) => {
            editor.updateSection(sectionId, patch);
        },
        [editor]
    );

    const handleUpdateBlockSettings = useCallback(
        (
            sectionId: string,
            blockId: string,
            patch: Record<string, any>,
            parentPath: string[] = []
        ) => {
            editor.updateBlock(sectionId, blockId, patch, parentPath);
        },
        [editor]
    );

    const handleRemoveBlock = useCallback(
        (sectionId: string, blockId: string, parentPath: string[] = []) => {
            const activeBlockId =
                blockPath.length > 0 ? blockPath[blockPath.length - 1] : null;
            const wasSelected =
                selectedSection === sectionId && activeBlockId === blockId;

            editor.removeBlock(sectionId, blockId, parentPath);

            if (wasSelected) {
                if (parentPath.length > 0) {
                    editor.selectBlock(sectionId, parentPath);
                } else {
                    editor.selectSection(sectionId);
                }
            }
        },
        [editor, selectedSection, blockPath]
    );

    const handleToggleBlockDisabled = useCallback(
        (sectionId: string, blockId: string, parentPath: string[] = []) => {
            editor.blocks.toggleDisabled(sectionId, blockId, parentPath);
        },
        [editor]
    );

    const handleDuplicateBlock = useCallback(
        (sectionId: string, blockId: string, parentPath: string[] = []) => {
            editor.blocks.duplicate(sectionId, blockId, parentPath);
        },
        [editor]
    );

    const handleRemoveSection = useCallback(
        (sectionId: string) => {
            if (selectedSection === sectionId) {
                editor.clearSelection();
            }
            editor.removeSection(sectionId);
        },
        [editor, selectedSection]
    );

    const handleDuplicateSection = useCallback(
        (sectionId: string) => {
            const newId = editor.sections.duplicate(sectionId);
            if (newId) {
                editor.selectSection(newId);
            }
        },
        [editor]
    );

    const handleToggleSectionDisabled = useCallback(
        (sectionId: string) => {
            editor.sections.toggleDisabled(sectionId);
        },
        [editor]
    );

    const handleRenameSection = useCallback(
        (sectionId: string, name: string) => {
            editor.renameSection(sectionId, name);
        },
        [editor]
    );

    const handleRenameBlock = useCallback(
        (
            sectionId: string,
            blockId: string,
            name: string,
            parentPath: string[] = []
        ) => {
            editor.renameBlock(sectionId, blockId, name, parentPath);
        },
        [editor]
    );

    // ── Section-level data ────────────────────────────────────────────────
    const sectionMeta = section ? sections[section.type] : null;
    const schema = sectionMeta?.schema;
    const sectionName = schema?.name || selectedSection || "";
    const sectionSettings = schema?.settings || [];

    // ── Active block: resolved by walking the full blockPath ──────────────
    const activeBlock = useMemo(
        () => resolveBlockByPath(section, blockPath),
        // eslint-disable-next-line react-hooks/exhaustive-deps
        [section, blockPath.join(",")]
    );

    /**
     * Path to the parent of the active block.
     * e.g. blockPath=["rowId","colId"] → parentPath=["rowId"]
     *      blockPath=["rowId"]         → parentPath=[]  (direct section child)
     */
    const parentPath: string[] = blockPath.slice(0, -1);

    // Full schema for the active block (resolving through the hierarchy to respect section-level block schemas).
    const activeBlockSchema = useMemo((): BlockSchema | null => {
        if (!activeBlock || !section || !schema) return null;

        let currentAllowedTypes = expandBlockTypes(
            (schema.blocks as any[]) || [],
            themeBlocks
        );
        let currentSchema: BlockSchema | null = null;
        let currentInstances = section.blocks || {};

        for (const blockId of blockPath) {
            const instance = currentInstances[blockId];
            if (!instance) return null;

            const typeSchema = currentAllowedTypes.find(
                (t) => t.type === instance.type
            );
            if (!typeSchema) {
                // Fallback to global theme block if not found in allowed types
                currentSchema = themeBlocks[instance.type]?.schema || null;
            } else {
                currentSchema = typeSchema;
            }

            if (!currentSchema) return null;

            currentInstances = instance.blocks || {};
            currentAllowedTypes = expandBlockTypes(
                (currentSchema.blocks as any[]) || [],
                themeBlocks
            );
        }

        return currentSchema;
    }, [activeBlock, section, schema, blockPath, themeBlocks]);

    // ── Early returns (after ALL hooks) ───────────────────────────────────
    if (!selectedSection || !section) {
        return (
            <div className="flex flex-col h-full bg-white">
                {/* Persistent header so the sidebar always has structure */}
                <div className="shrink-0 px-4 py-3 border-b border-gray-200">
                    <h2 className="text-sm font-bold text-gray-400">
                        Section settings
                    </h2>
                </div>

                {/* Placeholder body */}
                <div className="flex flex-col flex-1 p-4 gap-3 select-none">
                    <p className="text-sm font-medium text-gray-400">
                        No section selected
                    </p>
                    <p className="text-xs text-gray-300 leading-relaxed">
                        Click a section in the canvas or select one from the
                        panel on the left to edit its settings here.
                    </p>
                </div>
            </div>
        );
    }

    // ── Block settings view ───────────────────────────────────────────────
    if (activeBlock && selectedBlock) {
        const blockSchema = activeBlockSchema as BlockSchema;
        const schemaName = blockSchema?.name || activeBlock.type;
        const displayName = activeBlock._name || schemaName;
        const blockSettings: SettingSchema[] = blockSchema?.settings || [];

        return (
            <div className="flex flex-col h-full bg-white">
                <SettingsHeader
                    title={displayName}
                    titlePlaceholder={schemaName}
                    showBack={true}
                    onBack={goBack}
                    onRename={(name) =>
                        handleRenameBlock(
                            selectedSection,
                            selectedBlock,
                            name,
                            parentPath
                        )
                    }
                    actions={
                        <SettingsActions
                            isHidden={!!activeBlock.disabled}
                            onDuplicate={() =>
                                handleDuplicateBlock(
                                    selectedSection,
                                    selectedBlock,
                                    parentPath
                                )
                            }
                            onHide={() =>
                                handleToggleBlockDisabled(
                                    selectedSection,
                                    selectedBlock,
                                    parentPath
                                )
                            }
                            onRemove={() =>
                                handleRemoveBlock(
                                    selectedSection,
                                    selectedBlock,
                                    parentPath
                                )
                            }
                            removeLabel="Remove block"
                        />
                    }
                />

                <div className="flex-1 overflow-y-auto sidebar-scroll">
                    <div className="px-4 py-4">
                        {blockSettings.length === 0 && (
                            <p className="text-xs text-gray-400 text-center py-4">
                                No settings for this block.
                            </p>
                        )}
                        {blockSettings.map((s, idx) => (
                            <FieldRenderer
                                key={s.id || `idx-${idx}`}
                                setting={s}
                                value={activeBlock.settings?.[s.id]}
                                onChange={(val) =>
                                    handleUpdateBlockSettings(
                                        selectedSection,
                                        selectedBlock,
                                        { [s.id]: val },
                                        parentPath
                                    )
                                }
                            />
                        ))}
                    </div>
                </div>

                <div className="shrink-0 px-4 py-3 border-t border-gray-100">
                    <button
                        onClick={() =>
                            handleRemoveBlock(
                                selectedSection,
                                selectedBlock,
                                parentPath
                            )
                        }
                        className="w-full flex items-center justify-center gap-2 py-2 text-xs font-semibold text-red-500 hover:text-red-700 hover:bg-red-50 rounded-md transition-colors"
                    >
                        <Trash2 size={12} />
                        Remove block
                    </button>
                </div>
            </div>
        );
    }

    // ── Section settings  ────────────────────────────────
    return (
        <div className="flex flex-col h-full bg-white">
            <SettingsHeader
                title={section._name || sectionName}
                showBack={!layout.isRightSidebar}
                onBack={goBack}
                onRename={(name) => handleRenameSection(selectedSection, name)}
                actions={
                    <SettingsActions
                        isHidden={!!section.disabled}
                        size="sm"
                        onDuplicate={
                            isLayoutSection
                                ? undefined
                                : () => handleDuplicateSection(selectedSection)
                        }
                        onHide={() =>
                            handleToggleSectionDisabled(selectedSection)
                        }
                        onRemove={
                            isLayoutSection
                                ? undefined
                                : () => handleRemoveSection(selectedSection)
                        }
                        removeLabel="Remove section"
                    />
                }
            />

            <div className="flex-1 overflow-y-auto sidebar-scroll">
                <div className="px-4 py-4">
                    {sectionSettings.length > 0 && (
                        <>
                            {sectionSettings.map((s, idx) => (
                                <FieldRenderer
                                    key={s.id || `idx-${idx}`}
                                    setting={s}
                                    value={section.settings?.[s.id]}
                                    onChange={(val) =>
                                        handleUpdateSectionSettings(
                                            selectedSection,
                                            { [s.id]: val }
                                        )
                                    }
                                />
                            ))}
                        </>
                    )}

                    {sectionSettings.length === 0 && (
                        <p className="text-xs text-gray-400 text-center py-4">
                            No settings for this section.
                        </p>
                    )}

                    <div className="mt-5 pt-4 border-t border-gray-100 space-y-3">
                        <p className="text-[11px] font-semibold uppercase tracking-wide text-gray-500">
                            Universal section controls
                        </p>

                        <div>
                            <label className="block text-xs font-semibold text-gray-600 mb-1.5">
                                Section id
                            </label>
                            <input
                                type="text"
                                value={section.settings?.pb_section_id ?? ""}
                                onChange={(e) =>
                                    handleUpdateSectionSettings(selectedSection, {
                                        pb_section_id: e.target.value,
                                    })
                                }
                                placeholder="hero-main"
                                className="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-colors"
                            />
                        </div>

                        <div>
                            <label className="block text-xs font-semibold text-gray-600 mb-1.5">
                                Extra CSS classes
                            </label>
                            <input
                                type="text"
                                value={section.settings?.pb_section_class ?? ""}
                                onChange={(e) =>
                                    handleUpdateSectionSettings(selectedSection, {
                                        pb_section_class: e.target.value,
                                    })
                                }
                                placeholder="bg-slate-50 rounded-2xl"
                                className="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-colors"
                            />
                        </div>

                        <div>
                            <label className="block text-xs font-semibold text-gray-600 mb-1.5">
                                Background color
                            </label>
                            <input
                                type="text"
                                value={section.settings?.pb_bg_color ?? ""}
                                onChange={(e) =>
                                    handleUpdateSectionSettings(selectedSection, {
                                        pb_bg_color: e.target.value,
                                    })
                                }
                                placeholder="#f8fafc or rgba(0,0,0,.1)"
                                className="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-colors"
                            />
                        </div>

                        <div className="grid grid-cols-2 gap-2">
                            <div>
                                <label className="block text-xs font-semibold text-gray-600 mb-1.5">
                                    Padding top
                                </label>
                                <input
                                    type="text"
                                    value={section.settings?.pb_padding_top ?? ""}
                                    onChange={(e) =>
                                        handleUpdateSectionSettings(
                                            selectedSection,
                                            { pb_padding_top: e.target.value }
                                        )
                                    }
                                    placeholder="24px"
                                    className="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-colors"
                                />
                            </div>
                            <div>
                                <label className="block text-xs font-semibold text-gray-600 mb-1.5">
                                    Padding bottom
                                </label>
                                <input
                                    type="text"
                                    value={section.settings?.pb_padding_bottom ?? ""}
                                    onChange={(e) =>
                                        handleUpdateSectionSettings(
                                            selectedSection,
                                            { pb_padding_bottom: e.target.value }
                                        )
                                    }
                                    placeholder="24px"
                                    className="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-colors"
                                />
                            </div>
                        </div>

                        <div className="grid grid-cols-2 gap-2">
                            <div>
                                <label className="block text-xs font-semibold text-gray-600 mb-1.5">
                                    Margin top
                                </label>
                                <input
                                    type="text"
                                    value={section.settings?.pb_margin_top ?? ""}
                                    onChange={(e) =>
                                        handleUpdateSectionSettings(
                                            selectedSection,
                                            { pb_margin_top: e.target.value }
                                        )
                                    }
                                    placeholder="16px"
                                    className="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-colors"
                                />
                            </div>
                            <div>
                                <label className="block text-xs font-semibold text-gray-600 mb-1.5">
                                    Margin bottom
                                </label>
                                <input
                                    type="text"
                                    value={section.settings?.pb_margin_bottom ?? ""}
                                    onChange={(e) =>
                                        handleUpdateSectionSettings(
                                            selectedSection,
                                            { pb_margin_bottom: e.target.value }
                                        )
                                    }
                                    placeholder="16px"
                                    className="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-colors"
                                />
                            </div>
                        </div>

                        <div className="grid grid-cols-2 gap-2">
                            <div>
                                <label className="block text-xs font-semibold text-gray-600 mb-1.5">
                                    Border radius
                                </label>
                                <input
                                    type="text"
                                    value={section.settings?.pb_border_radius ?? ""}
                                    onChange={(e) =>
                                        handleUpdateSectionSettings(
                                            selectedSection,
                                            { pb_border_radius: e.target.value }
                                        )
                                    }
                                    placeholder="12px"
                                    className="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-colors"
                                />
                            </div>
                            <div>
                                <label className="block text-xs font-semibold text-gray-600 mb-1.5">
                                    Border color
                                </label>
                                <input
                                    type="text"
                                    value={section.settings?.pb_border_color ?? ""}
                                    onChange={(e) =>
                                        handleUpdateSectionSettings(
                                            selectedSection,
                                            { pb_border_color: e.target.value }
                                        )
                                    }
                                    placeholder="#e5e7eb"
                                    className="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-colors"
                                />
                            </div>
                        </div>

                        <div>
                            <label className="block text-xs font-semibold text-gray-600 mb-1.5">
                                Border width
                            </label>
                            <input
                                type="text"
                                value={section.settings?.pb_border_width ?? ""}
                                onChange={(e) =>
                                    handleUpdateSectionSettings(selectedSection, {
                                        pb_border_width: e.target.value,
                                    })
                                }
                                placeholder="1px"
                                className="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-colors"
                            />
                        </div>

                        <div>
                            <label className="block text-xs font-semibold text-gray-600 mb-1.5">
                                Overlay color
                            </label>
                            <input
                                type="text"
                                value={section.settings?.pb_overlay_color ?? ""}
                                onChange={(e) =>
                                    handleUpdateSectionSettings(selectedSection, {
                                        pb_overlay_color: e.target.value,
                                    })
                                }
                                placeholder="rgba(0,0,0,.35)"
                                className="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-colors"
                            />
                            <p className="mt-1 text-[11px] text-gray-400">
                                Adds a full overlay layer above this section.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {!isLayoutSection && (
                <div className="shrink-0 px-4 py-3 border-t border-gray-100">
                    <button
                        onClick={() => handleRemoveSection(selectedSection)}
                        className="w-full flex items-center justify-center gap-2 py-2 text-xs font-semibold text-red-500 hover:text-red-700 hover:bg-red-50 rounded-md transition-colors"
                    >
                        <Trash2 size={12} />
                        Remove section
                    </button>
                </div>
            )}
        </div>
    );
}

export default memo(SettingsPanel);
