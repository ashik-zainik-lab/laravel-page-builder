import React, { memo } from "react";
import { SettingSchema } from "@/types/page-builder";

interface ParagraphFieldProps {
    setting: SettingSchema;
}

function ParagraphField({ setting }: ParagraphFieldProps) {
    return (
        <div className="mb-3 text-xs text-gray-500 leading-relaxed">
            {setting.content || setting.label}
        </div>
    );
}

export default memo(ParagraphField);
