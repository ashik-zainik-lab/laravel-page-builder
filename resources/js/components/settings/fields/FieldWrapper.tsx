import React from "react";
import { SettingSchema } from "@/types/page-builder";

export interface FieldWrapperProps {
    setting: SettingSchema;
    children: React.ReactNode;
    /** Omit top-level label (used by Checkbox, Range, Header, Paragraph) */
    noLabel?: boolean;
    /** Extra class on the outer div */
    className?: string;
}

export function FieldWrapper({
    setting,
    children,
    noLabel = false,
    className = "mb-4",
}: FieldWrapperProps) {
    return (
        <div className={className} data-setting-id={setting.id}>
            {!noLabel && setting.label && (
                <label className="block text-[11px] font-semibold text-gray-500 mb-1.5 tracking-wide">
                    {setting.label}
                </label>
            )}
            {children}
            {setting.info && (
                <p className="text-[10px] text-gray-400 mt-1 leading-relaxed">
                    {setting.info}
                </p>
            )}
        </div>
    );
}

export default FieldWrapper;
