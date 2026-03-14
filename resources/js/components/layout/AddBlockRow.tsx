import React from "react";
import { Plus } from "lucide-react";

/* ── "Add block" inline row ──────────────────────────────────────────── */

interface AddBlockRowProps {
    depth: number;
    onAdd: () => void;
}

export default function AddBlockRow({ depth, onAdd }: AddBlockRowProps) {
    return (
        <div
            onClick={onAdd}
            className="flex items-center gap-1.5 py-1.5 cursor-pointer group select-none transition-colors"
            style={{ paddingLeft: `${depth * 16 + 12}px`, paddingRight: 12 }}
        >
            <Plus
                size={12}
                strokeWidth={2.5}
                className="text-gray-400 group-hover:text-blue-500 shrink-0 transition-colors"
            />
            <span className="text-[11px] text-gray-400 group-hover:text-blue-500 font-medium transition-colors">
                Add block
            </span>
        </div>
    );
}
