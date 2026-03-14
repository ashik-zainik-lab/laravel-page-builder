import React, { memo } from "react";
import { SettingSchema } from "@/types/page-builder";

interface CheckboxFieldProps {
    setting: SettingSchema;
    value: boolean;
    onChange: (val: boolean) => void;
}

function CheckboxField({ setting, value, onChange }: CheckboxFieldProps) {
    return (
        <label className="flex items-center gap-2 cursor-pointer">
            <input
                type="checkbox"
                className="w-3.5 h-3.5 rounded accent-blue-500 cursor-pointer"
                checked={!!value}
                onChange={(e) => onChange(e.target.checked)}
            />
            <span className="text-xs text-gray-700 font-medium">
                {setting.label}
            </span>
        </label>
    );
}

export default memo(CheckboxField);
