import React, { useCallback, useEffect, useSyncExternalStore } from "react";
import { DeviceSwitcher, UndoRedoControls, EditorLogo } from "./header";
import { Crosshair, ExternalLink, LogOut } from "lucide-react";
import { cn } from "@/lib/utils";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "./ui/select";
import { useEditorInstance } from "@/core/editorContext";
import { useEditorNavigation } from "@/hooks/useEditorNavigation";
import { useEditorLayout } from "@/hooks/useEditorLayout";
import { useStore } from "@/core/store/useStore";
import { useMaxBreakpoint } from "@/hooks/useBreakpoint";
import config from "@/config";
import api from "@/services/api";

/**
 * Top header bar — reads all state directly from the editor context,
 * store, and manager hooks. No props required.
 */
export default function EditorHeader() {
  const editor = useEditorInstance();
  const { pages, saving, pageIsActive, setPageIsActive } = useStore();
  const { slug, device, setPage, setDevice } = useEditorNavigation();
  const preserved = config.preservedPages ?? [];
  const slugLower = (slug ?? "").toLowerCase();
  const isPreservedPage = preserved.includes(slugLower);
  const { inspectorEnabled } = useEditorLayout();
  const isMobile = useMaxBreakpoint(768);

  useEffect(() => {
    if (isPreservedPage) {
      setPageIsActive(true);
    }
  }, [isPreservedPage, setPageIsActive]);

  // Subscribe to history manager for reactive canUndo / canRedo.
  useSyncExternalStore(
    useCallback((listener) => editor.history.subscribe(listener), [editor]),
    useCallback(() => editor.history.getVersion(), [editor]),
    () => 0,
  );
  const canUndo = editor.history.canUndo;
  const canRedo = editor.history.canRedo;

  return (
    <header className="flex items-center h-[52px] px-3 bg-white border-b border-gray-200 flex-shrink-0 gap-1 z-50">
      {/* ── Left: Logo + Page selector ─────────────────────────── */}
      <div className="flex items-center gap-1.5 flex-shrink-0">
        <button
          type="button"
          onClick={() => {
            window.dispatchEvent(new CustomEvent("pagebuilder:exit"));
          }}
          className="p-1.5 rotate-180 rounded-lg text-gray-400 hover:bg-gray-100 transition-colors"
          title="Exit Editor"
        >
          <LogOut className="w-[18px] h-[18px]" />
        </button>

        <EditorLogo />

        <div className="relative flex-shrink-0">
          <Select value={slug || ""} onValueChange={(value) => setPage(value)}>
            <SelectTrigger className="h-8 w-[160px] bg-transparent border-none hover:bg-gray-100 focus:ring-0 focus:ring-offset-0 font-medium text-gray-800">
              <SelectValue placeholder="Select page…" />
            </SelectTrigger>
            <SelectContent>
              {pages.map((p) => (
                <SelectItem key={p.slug} value={p.slug}>
                  {p.title}
                  {p.is_active === false ? " (Draft)" : ""}
                </SelectItem>
              ))}
            </SelectContent>
          </Select>
        </div>
      </div>

      {/* ── Center: Device switcher + Undo/Redo ────────────────── */}
      <div className="flex items-center justify-center flex-1 gap-1">
        <button
          type="button"
          onClick={() => editor.layout.toggleInspector()}
          title={`Inspector mode (${
            inspectorEnabled ? "active" : "inactive"
          }) – Press Cmd+I to toggle`}
          className={cn(
            "flex items-center justify-center w-8 h-8 rounded-lg transition-all duration-200",
            inspectorEnabled
              ? "text-blue-600 bg-blue-50 hover:bg-blue-100 active:bg-blue-200"
              : "text-gray-400 hover:bg-gray-50 active:bg-gray-100",
          )}
        >
          <Crosshair className="w-[18px] h-[18px]" />
        </button>

        {!isMobile && <div className="w-px h-5 bg-gray-200 mx-0.5" />}

        {!isMobile && (
          <DeviceSwitcher device={device} onDeviceChange={setDevice} />
        )}

        <div className="w-px h-5 bg-gray-200 mx-0.5" />

        <UndoRedoControls
          canUndo={canUndo}
          canRedo={canRedo}
          onUndo={() => editor.undo()}
          onRedo={() => editor.redo()}
        />
      </div>

      {/* ── Right: Draft / Live + Save ──────────────────────────── */}
      <div className="flex items-center gap-1.5 sm:gap-2 flex-shrink-0 min-w-0">
        {isPreservedPage ? (
          <span
            className="inline-flex items-center rounded-lg border border-gray-200 bg-gray-50 px-2 py-1 text-[10px] sm:text-xs font-medium text-gray-600 whitespace-nowrap"
            title="This system page is always visible on the site"
          >
            Always live
          </span>
        ) : (
          <div
            className="flex items-center rounded-lg border border-gray-200 p-0.5 text-[10px] sm:text-xs font-medium flex-shrink-0"
            title="Draft pages are hidden from public URLs until you switch to Live and Save"
          >
            <button
              type="button"
              onClick={() => setPageIsActive(false)}
              className={cn(
                "px-2 sm:px-2.5 py-1 rounded-md transition-colors",
                !pageIsActive
                  ? "bg-gray-900 text-white"
                  : "text-gray-500 hover:bg-gray-50",
              )}
            >
              Draft
            </button>
            <button
              type="button"
              onClick={() => setPageIsActive(true)}
              className={cn(
                "px-2 sm:px-2.5 py-1 rounded-md transition-colors",
                pageIsActive
                  ? "bg-emerald-600 text-white"
                  : "text-gray-500 hover:bg-gray-50",
              )}
            >
              Live
            </button>
          </div>
        )}
        <button
          type="button"
          onClick={() => {
            if (!slug) return;
            window.open(
              api.getLiveUrl(slug),
              "_blank",
              "noopener,noreferrer",
            );
          }}
          disabled={!slug}
          title={
            !pageIsActive && !isPreservedPage
              ? "Open public URL — draft pages may 404 until you set Live and Save"
              : "Open this page on the live site (new tab)"
          }
          className="flex p-1.5 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-800 transition-colors
                        disabled:opacity-40 disabled:cursor-not-allowed shrink-0"
        >
          <ExternalLink className="w-[18px] h-[18px]" />
        </button>
        <button
          type="button"
          onClick={() => editor.pages.save()}
          disabled={saving || !slug}
          title="Save page (Ctrl+S or ⌘S)"
          className="px-4 py-1.5 rounded-lg text-sm font-semibold transition-all
                        bg-gray-900 text-white hover:bg-gray-800
                        disabled:opacity-40 disabled:cursor-not-allowed"
        >
          {saving ? "Saving…" : "Save"}
        </button>
      </div>
    </header>
  );
}
