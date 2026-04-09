import React, { memo } from "react";
import { Layers, ListTree, FileText, Palette } from "lucide-react";
import { cn } from "@/lib/utils";
import { useDrawer } from "@/hooks/useDrawer";
import type { MobileDrawerPanel } from "@/core/editor/DrawerManager";

interface DockItem {
    id: MobileDrawerPanel;
    label: string;
    icon: React.ReactNode;
}

const DOCK_ITEMS: DockItem[] = [
    { id: "sections", label: "Layers", icon: <Layers size={20} /> },
    { id: "outline", label: "Nav", icon: <ListTree size={20} /> },
    { id: "page", label: "Pages", icon: <FileText size={20} /> },
    { id: "theme", label: "Theme", icon: <Palette size={20} /> },
];

/**
 * MobileDock — bottom dock bar for the mobile editor.
 *
 * Renders a row of icon-label buttons, one per panel. Tapping a button
 * toggles the corresponding panel open/closed in the MobileDrawer.
 * The active panel button is highlighted in blue.
 *
 * Only rendered on small screens (controlled by the parent).
 */
function MobileDock() {
    const { activePanel, toggle } = useDrawer();

    return (
        <div className="flex items-center justify-around border-t border-gray-200 bg-white px-2 py-1 shrink-0">
            {DOCK_ITEMS.map((item) => (
                <button
                    key={item.id}
                    type="button"
                    title={item.label}
                    onClick={() => toggle(item.id)}
                    className={cn(
                        "flex flex-col items-center gap-0.5 flex-1 py-2 rounded-lg transition-colors text-xs font-medium",
                        activePanel === item.id
                            ? "text-blue-600"
                            : "text-gray-500 hover:text-gray-700"
                    )}
                >
                    {item.icon}
                    <span>{item.label}</span>
                </button>
            ))}
        </div>
    );
}

export default memo(MobileDock);
