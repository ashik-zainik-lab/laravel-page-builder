import React, { memo } from "react";
import { SettingSchema } from "@/types/page-builder";

interface RadioFieldProps {
    setting: SettingSchema;
    value: string;
    onChange: (val: string) => void;
}

function RadioField({ setting, value, onChange }: RadioFieldProps) {
    return (
        <div className="flex flex-col gap-1.5">
            {(setting.options || []).map((opt) => (
                <label
                    key={opt.value}
                    className="flex items-center gap-2 cursor-pointer"
                >
                    <input
                        type="radio"
                        name={`radio-${setting.id}`}
                        className="w-3.5 h-3.5 accent-blue-500 cursor-pointer"
                        checked={value === opt.value}
                        onChange={() => onChange(opt.value)}
                    />
                    <span className="text-xs text-gray-700">{opt.label}</span>
                </label>
            ))}
        </div>
    );
}

export default memo(RadioField);
