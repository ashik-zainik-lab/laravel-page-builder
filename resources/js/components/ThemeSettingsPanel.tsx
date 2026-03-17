import { memo, useCallback } from "react";
import { useStore } from "@/core/store/useStore";
import { useEditorInstance } from "@/core/editorContext";
import { ResetIcon } from "@/components/header/icons";
import FieldRenderer from "./settings/fields/FieldRenderer";
import type { ThemeSettingsGroup, SettingSchema } from "@/types/page-builder";

/**
 * Panel for editing global theme settings.
 * Reads themeSettings directly from the Zustand store and calls
 * editor.pages methods for mutations — no props required.
 * Theme settings are saved alongside the page via the main Save button.
 */
function ThemeSettingsPanel() {
    const editor = useEditorInstance();
    const { themeSettings } = useStore();
    const { schema, values } = themeSettings;

    const handleChange = useCallback(
        (key: string, val: SettingSchema["default"]) => {
            editor.pages.updateThemeSetting(key, val);
        },
        [editor]
    );

    const handleReset = useCallback(
        (key: string) => {
            editor.pages.resetThemeSetting(key);
        },
        [editor]
    );

    const handleResetAll = useCallback(() => {
        editor.pages.resetAllThemeSettings();
    }, [editor]);

    if (!schema || schema.length === 0) {
        return (
            <div className="flex flex-col flex-1 p-4 gap-3 select-none">
                <p className="text-sm font-medium text-gray-400">
                    No theme settings configured
                </p>
                <p className="text-xs text-gray-300 leading-relaxed">
                    Define a theme settings schema in your{" "}
                    <code className="bg-gray-100 px-1 rounded text-[11px]">
                        pagebuilder.php
                    </code>{" "}
                    config to enable global theme customisation.
                </p>
            </div>
        );
    }

    return (
        <div className="flex flex-col flex-1 overflow-hidden">
            {/* Panel header */}
            <div className="flex items-center justify-between px-4 py-2 border-b border-gray-100">
                <span className="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    Theme Settings
                </span>
                <button
                    type="button"
                    onClick={handleResetAll}
                    title="Reset all settings to defaults"
                    className="flex items-center gap-1 text-xs text-gray-400 hover:text-gray-600 transition-colors"
                >
                    <ResetIcon className="w-3 h-3" />
                    Reset all
                </button>
            </div>

            <div className="flex-1 overflow-y-auto sidebar-scroll">
                {schema.map((group: ThemeSettingsGroup, groupIdx: number) => (
                    <div key={group.name || `group-${groupIdx}`}>
                        {group.name && (
                            <div className="px-4 pt-4 pb-1">
                                <h3 className="text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    {group.name}
                                </h3>
                            </div>
                        )}

                        <div className="px-4 py-3 border-b border-gray-100">
                            {group.settings.map(
                                (setting: SettingSchema, idx: number) => {
                                    const settingKey =
                                        setting.key ?? setting.id;
                                    const currentValue =
                                        values?.[settingKey];
                                    const isModified =
                                        currentValue !== undefined &&
                                        currentValue !== setting.default;

                                    return (
                                        <div
                                            key={
                                                settingKey ||
                                                `s-${groupIdx}-${idx}`
                                            }
                                            className="relative group/setting"
                                        >
                                            <FieldRenderer
                                                setting={setting}
                                                value={currentValue}
                                                onChange={(val) =>
                                                    handleChange(
                                                        settingKey,
                                                        val
                                                    )
                                                }
                                            />
                                            {isModified && (
                                                <button
                                                    type="button"
                                                    onClick={() =>
                                                        handleReset(settingKey)
                                                    }
                                                    title="Reset to default"
                                                    className="absolute top-0 right-0 p-1 text-gray-300 hover:text-gray-500 opacity-0 group-hover/setting:opacity-100 transition-opacity"
                                                >
                                                    <ResetIcon className="w-3 h-3" />
                                                </button>
                                            )}
                                        </div>
                                    );
                                }
                            )}
                        </div>
                    </div>
                ))}
            </div>
        </div>
    );
}

export default memo(ThemeSettingsPanel);
