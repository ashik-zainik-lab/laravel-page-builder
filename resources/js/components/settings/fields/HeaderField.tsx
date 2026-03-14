import React, { memo } from "react";
import { SettingSchema } from "@/types/page-builder";

interface HeaderFieldProps {
    setting: SettingSchema;
}

function HeaderField({ setting }: HeaderFieldProps) {
    return (
        <div className="pt-3 pb-1 mb-2 border-t border-gray-200 first:border-t-0 first:pt-0">
            <span className="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                {setting.content || setting.label}
            </span>
        </div>
    );
}

export default memo(HeaderField);
