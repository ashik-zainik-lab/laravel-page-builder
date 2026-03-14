import React, { memo } from "react";
import { SettingSchema } from "@/types/page-builder";

interface RangeFieldProps {
    setting: SettingSchema;
    value: number;
    onChange: (val: number) => void;
}

function RangeField({ setting, value, onChange }: RangeFieldProps) {
    return (
        <>
            <div className="flex justify-between mb-1">
                <label className="text-[11px] font-semibold text-gray-500 uppercase tracking-wide">
                    {setting.label}
                </label>
                <span className="text-[11px] font-bold text-blue-600">
                    {value}
                    {setting.unit || ""}
                </span>
            </div>
            <input
                type="range"
                className="w-full accent-blue-500"
                min={setting.min ?? 0}
                max={setting.max ?? 100}
                step={setting.step ?? 1}
                value={value}
                onChange={(e) => onChange(Number(e.target.value))}
            />
            <div className="flex justify-between text-[10px] text-gray-400 mt-0.5">
                <span>{setting.min ?? 0}</span>
                <span>{setting.max ?? 100}</span>
            </div>
        </>
    );
}

export default memo(RangeField);
