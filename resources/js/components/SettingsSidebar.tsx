import React, { memo } from "react";
import SettingsPanel from "./SettingsPanel";
import { SidebarSkeleton } from "./ui/SidebarSkeleton";
import { useStore } from "@/core/store/useStore";

/**
 * Right sidebar shown only on large screens (dual-sidebar layout).
 * Displays section/block settings or a placeholder when nothing is selected.
 */
function SettingsSidebar() {
    const { loading } = useStore();

    return (
        <div className="w-80 min-w-[280px] max-w-[360px] bg-white border-l border-gray-200 flex flex-col overflow-hidden shrink-0 shadow-sm">
            {loading ? <SidebarSkeleton /> : <SettingsPanel />}
        </div>
    );
}

export default memo(SettingsSidebar);
