import React, { memo } from "react";
import { SettingSchema } from "@/types/page-builder";
import { inputCls } from "./TextField";
import { cn } from "@/lib/utils";

interface ColorFieldProps {
    setting: SettingSchema;
    value: string;
    onChange: (val: string) => void;
    /** If true, renders as a background (accepts gradients) */
    isBackground?: boolean;
}

function ColorField({
    setting,
    value,
    onChange,
    isBackground = false,
}: ColorFieldProps) {
    if (isBackground) {
        return (
            <div className="flex items-center gap-2">
                <div
                    className="w-8 h-8 rounded border border-gray-200 shrink-0"
                    style={{ background: value || "#000" }}
                />
                <input
                    type="text"
                    className={cn(inputCls, "flex-1")}
                    value={value}
                    placeholder="linear-gradient(…) or #hex"
                    onChange={(e) => onChange(e.target.value)}
                />
            </div>
        );
    }

    return (
        <div className="flex items-center gap-2">
            <input
                type="color"
                className="w-8 h-8 rounded cursor-pointer border border-gray-200 p-0.5"
                value={/^#[0-9a-fA-F]{3,8}$/.test(value) ? value : "#000000"}
                onChange={(e) => onChange(e.target.value)}
            />
            <input
                type="text"
                className={cn(inputCls, "flex-1")}
                value={value}
                onChange={(e) => onChange(e.target.value)}
            />
        </div>
    );
}

export default memo(ColorField);
