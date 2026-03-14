import React, { useCallback, useRef } from "react";
import { Eye, EyeOff, PanelTop, PanelBottom } from "lucide-react";
import { cn } from "@/lib/utils";
import { LayoutSectionInstance, SectionData } from "@/types/page-builder";

/* ── LayoutSectionRow ─────────────────────────────────────────────────
 *
 * A non-sortable, fixed row representing a layout section slot
 * (e.g. "header" above @yield, "footer" below @yield).
 *
 * Unlike SortableSectionRow, this row has no drag handle, no duplicate
 * action, and no remove action — layout slots are structural and their
 * position is determined by @sections() calls in the Blade layout.
 * ─────────────────────────────────────────────────────────────────── */

interface LayoutSectionRowProps {
    /** Position key used by @sections() directive (e.g. "header", "footer"). */
    sectionKey: string;
    /** The stored per-page override for this layout slot. */
    section: LayoutSectionInstance;
    /** Schema/meta from the SectionRegistry for this section type. */
    meta: SectionData | undefined;
    /** Which position this slot occupies in the layout. */
    position: "top" | "bottom";
    /** Whether this row is currently selected (highlighted in settings panel). */
    isSelected: boolean;
    onSelect: (sectionKey: string) => void;
    onToggleDisabled: (sectionKey: string) => void;
    onHover: (sectionKey: string | null) => void;
}

export default function LayoutSectionRow({
    sectionKey,
    section,
    meta,
    position,
    isSelected,
    onSelect,
    onToggleDisabled,
    onHover,
}: LayoutSectionRowProps) {
    const isDisabled = !!section.disabled;
    const name = section._name || meta?.schema?.name || sectionKey;

    const Icon = position === "top" ? PanelTop : PanelBottom;

    // Debounced hover (mirrors SortableSectionRow pattern)
    const hoverTimerRef = useRef<ReturnType<typeof setTimeout> | null>(null);
    const handleMouseEnter = useCallback(() => {
        hoverTimerRef.current = setTimeout(() => {
            onHover(sectionKey);
        }, 60);
    }, [onHover, sectionKey]);
    const handleMouseLeave = useCallback(() => {
        if (hoverTimerRef.current) {
            clearTimeout(hoverTimerRef.current);
            hoverTimerRef.current = null;
        }
        onHover(null);
    }, [onHover]);
    React.useEffect(() => {
        return () => {
            if (hoverTimerRef.current) clearTimeout(hoverTimerRef.current);
        };
    }, []);

    return (
        <div
            data-layout-section-key={sectionKey}
            className={cn(
                "group flex items-center px-2 py-[8px] cursor-pointer transition-colors select-none",
                isDisabled && "opacity-50"
            )}
            onClick={() => onSelect(sectionKey)}
            onMouseEnter={handleMouseEnter}
            onMouseLeave={handleMouseLeave}
        >
            {/* Fixed-width spacer to align with sortable rows (chevron + 2px gap) */}
            <div className="w-4 h-4 shrink-0 mr-0.5" />

            {/* Position icon (non-interactive, replaces drag handle) */}
            <div className="w-4 h-4 flex items-center justify-center shrink-0 mr-2">
                <Icon
                    size={14}
                    strokeWidth={1.5}
                    className={cn(
                        isSelected
                            ? "text-blue-500"
                            : "text-gray-400 group-hover:text-gray-600 transition-colors"
                    )}
                />
            </div>

            {/* Section name */}
            <span
                className={cn(
                    "flex-1 text-xs font-semibold truncate transition-colors",
                    isSelected
                        ? "text-blue-600"
                        : "text-gray-700 group-hover:text-gray-900"
                )}
            >
                {name}
            </span>

            {/* Toggle visibility — always visible so user knows they can hide a layout section */}
            <button
                title={isDisabled ? "Show section" : "Hide section"}
                onClick={(e) => {
                    e.stopPropagation();
                    onToggleDisabled(sectionKey);
                }}
                className={cn(
                    "p-1 rounded transition-colors opacity-0 group-hover:opacity-100",
                    isDisabled
                        ? "text-gray-400 hover:text-blue-500 hover:bg-blue-100 !opacity-100"
                        : "text-gray-400 hover:text-gray-600 hover:bg-gray-200"
                )}
            >
                {isDisabled ? (
                    <EyeOff size={13} strokeWidth={1.5} />
                ) : (
                    <Eye size={13} strokeWidth={1.5} />
                )}
            </button>
        </div>
    );
}
