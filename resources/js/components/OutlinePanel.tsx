import React, { memo, useCallback, useMemo, useState } from "react";
import { ChevronDown, ChevronRight, ListTree } from "lucide-react";
import { cn } from "@/lib/utils";
import { useStore } from "@/core/store/useStore";
import { useEditorInstance } from "@/core/editorContext";
import { useEditorNavigation } from "@/hooks/useEditorNavigation";
import { useEditorLayout } from "@/hooks/useEditorLayout";
import { useBreakpoint } from "@/hooks/useBreakpoint";
import { drawerManager } from "@/core/editor/DrawerManager";
import type { BlockData, BlockInstance, SectionData } from "@/types/page-builder";

function pathsEqual(a: string[], b: string[]): boolean {
    if (a.length !== b.length) return false;
    return a.every((v, i) => v === b[i]);
}

function sectionTitle(
    instance: { type: string; _name?: string; layout?: boolean; layoutZone?: string },
    schemas: Record<string, SectionData>,
): string {
    if (instance._name) return instance._name;
    const meta = schemas[instance.type];
    if (meta?.schema?.name) return meta.schema.name;
    return instance.type;
}

function blockTitle(
    instance: BlockInstance,
    schemas: Record<string, BlockData>,
): string {
    if (instance._name) return instance._name;
    const meta = schemas[instance.type];
    if (meta?.schema?.name) return meta.schema.name;
    return instance.type;
}

interface BlockRowsProps {
    sectionId: string;
    blocks: Record<string, BlockInstance>;
    order: string[];
    parentPath: string[];
    depth: number;
    selectedSection: string | null;
    selectedPath: string[];
    blockSchemas: Record<string, BlockData>;
    onPickBlock: (sectionId: string, path: string[]) => void;
}

function BlockRows({
    sectionId,
    blocks,
    order,
    parentPath,
    depth,
    selectedSection,
    selectedPath,
    blockSchemas,
    onPickBlock,
}: BlockRowsProps): React.ReactNode {
    return (
        <>
            {order.map((blockId) => {
                const block = blocks[blockId];
                if (!block) return null;
                const path = [...parentPath, blockId];
                const childOrder = block.order ?? [];
                const childMap = block.blocks ?? {};
                const hasChildren = childOrder.length > 0;
                const isSelected =
                    selectedSection === sectionId && pathsEqual(selectedPath, path);

                return (
                    <div key={path.join("/")} className="select-none">
                        <button
                            type="button"
                            onClick={() => onPickBlock(sectionId, path)}
                            style={{ paddingLeft: 8 + depth * 12 }}
                            className={cn(
                                "w-full text-left flex items-center gap-1 py-1 pr-2 text-xs rounded-md transition-colors",
                                isSelected
                                    ? "bg-blue-50 text-blue-900 font-medium"
                                    : "text-gray-700 hover:bg-gray-100",
                            )}
                        >
                            {hasChildren ? (
                                <span className="w-4 h-4 shrink-0 inline-flex items-center justify-center text-gray-400">
                                    <ChevronDown className="w-3.5 h-3.5" aria-hidden />
                                </span>
                            ) : (
                                <span className="w-4 shrink-0" aria-hidden />
                            )}
                            <span className="truncate">
                                {blockTitle(block, blockSchemas)}
                            </span>
                            <span className="ml-auto shrink-0 text-[10px] text-gray-400 font-mono">
                                {block.type}
                            </span>
                        </button>
                        {hasChildren && (
                            <BlockRows
                                sectionId={sectionId}
                                blocks={childMap}
                                order={childOrder}
                                parentPath={path}
                                depth={depth + 1}
                                selectedSection={selectedSection}
                                selectedPath={selectedPath}
                                blockSchemas={blockSchemas}
                                onPickBlock={onPickBlock}
                            />
                        )}
                    </div>
                );
            })}
        </>
    );
}

/**
 * Webflow-style Navigator: flat tree of sections and nested blocks.
 * Clicking an item selects it; on single-sidebar / mobile, switches to the
 * layers panel so the inspector opens.
 */
function OutlinePanel() {
    const editor = useEditorInstance();
    const layout = useEditorLayout();
    const isDesktop = useBreakpoint(768);
    const currentPage = useStore((s) => s.currentPage);
    const sectionSchemas = useStore((s) => s.sections);
    const blockSchemas = useStore((s) => s.blocks);
    const { selectedSection, blockPath } = useEditorNavigation({
        registerAdapter: false,
    });

    const [expanded, setExpanded] = useState<Record<string, boolean>>({});

    const order = currentPage?.order ?? [];

    const toggleSection = useCallback((id: string) => {
        setExpanded((prev) => ({ ...prev, [id]: !prev[id] }));
    }, []);

    const afterPick = useCallback(() => {
        if (!isDesktop) {
            drawerManager.open("sections");
            return;
        }
        if (!layout.isDualSidebar) {
            editor.layout.setSidebarTab("sections");
        }
    }, [editor, isDesktop, layout.isDualSidebar]);

    const pickSection = useCallback(
        (sectionId: string) => {
            editor.selectSection(sectionId);
            afterPick();
        },
        [editor, afterPick],
    );

    const pickBlock = useCallback(
        (sectionId: string, path: string[]) => {
            editor.selectBlock(sectionId, path);
            afterPick();
        },
        [editor, afterPick],
    );

    const rows = useMemo(() => {
        if (!currentPage) return null;
        return order.map((sectionId) => {
            const instance = currentPage.sections[sectionId];
            if (!instance) return null;
            const title = sectionTitle(instance, sectionSchemas);
            const blockOrder = instance.order ?? [];
            const blockMap = instance.blocks ?? {};
            const hasBlocks = blockOrder.length > 0;
            const isOpen = expanded[sectionId] !== false;
            const zone =
                instance.layout && instance.layoutZone
                    ? instance.layoutZone
                    : null;
            const sectionSelected =
                selectedSection === sectionId && blockPath.length === 0;

            return (
                <div key={sectionId} className="border-b border-gray-100 last:border-0">
                    <div className="flex items-stretch gap-0">
                        {hasBlocks ? (
                            <button
                                type="button"
                                aria-expanded={isOpen}
                                onClick={() => toggleSection(sectionId)}
                                className="shrink-0 px-1 flex items-center text-gray-400 hover:text-gray-600"
                                title={isOpen ? "Collapse" : "Expand"}
                            >
                                {isOpen ? (
                                    <ChevronDown className="w-4 h-4" />
                                ) : (
                                    <ChevronRight className="w-4 h-4" />
                                )}
                            </button>
                        ) : (
                            <span className="w-6 shrink-0" />
                        )}
                        <button
                            type="button"
                            onClick={() => pickSection(sectionId)}
                            className={cn(
                                "flex-1 text-left py-2 pr-2 flex items-center gap-2 min-w-0 rounded-md -ml-1 pl-1",
                                sectionSelected
                                    ? "bg-blue-50 text-blue-900"
                                    : "hover:bg-gray-50 text-gray-800",
                            )}
                        >
                            <span className="text-xs font-semibold truncate">
                                {title}
                            </span>
                            {zone && (
                                <span className="shrink-0 text-[10px] uppercase tracking-wide text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded">
                                    {zone}
                                </span>
                            )}
                            <span className="ml-auto shrink-0 text-[10px] text-gray-400 font-mono">
                                {instance.type}
                            </span>
                        </button>
                    </div>
                    {hasBlocks && isOpen && (
                        <div className="pb-2 pl-1">
                            <BlockRows
                                sectionId={sectionId}
                                blocks={blockMap}
                                order={blockOrder}
                                parentPath={[]}
                                depth={0}
                                selectedSection={selectedSection}
                                selectedPath={blockPath}
                                blockSchemas={blockSchemas}
                                onPickBlock={pickBlock}
                            />
                        </div>
                    )}
                </div>
            );
        });
    }, [
        blockPath,
        blockSchemas,
        currentPage,
        expanded,
        order,
        pickBlock,
        pickSection,
        sectionSchemas,
        selectedSection,
        toggleSection,
    ]);

    if (!currentPage) {
        return (
            <div className="px-4 py-6 text-center text-sm text-gray-400">
                Load a page to use the Navigator.
            </div>
        );
    }

    return (
        <div className="flex flex-col h-full min-h-0">
            <div className="px-4 py-3 border-b border-gray-100 shrink-0">
                <div className="flex items-center gap-2 text-gray-800">
                    <ListTree className="w-4 h-4 text-gray-500" aria-hidden />
                    <span className="text-sm font-semibold">Navigator</span>
                </div>
                <p className="mt-1 text-[11px] text-gray-500 leading-snug">
                    Click a section or block to select it. On a narrow layout,
                    the editor opens the inspector automatically.
                </p>
            </div>
            <div className="flex-1 overflow-y-auto px-2 py-2">{rows}</div>
        </div>
    );
}

export default memo(OutlinePanel);
