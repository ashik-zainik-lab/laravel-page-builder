import React, { memo, useCallback, useEffect, useState } from "react";
import { Check, Copy, History, Trash2 } from "lucide-react";
import { useStore } from "@/core/store/useStore";
import { useEditorInstance } from "@/core/editorContext";
import type { PageMeta } from "@/types/page-builder";
import api from "@/services/api";
import { toast } from "@/core/toastStore";
import config from "@/config";
import { useEditorNavigation } from "@/hooks/useEditorNavigation";

/**
 * Panel for editing page-level metadata (SEO fields).
 * Reads meta directly from the Zustand store and calls
 * editor.pages.updateMeta() for mutations — no props required.
 */
function PageMetaPanel() {
    const editor = useEditorInstance();
    const { pageMeta: meta, currentSlug, pageTemplate } = useStore();
    const loadPage = useStore((s) => s.loadPage);
    const loadPages = useStore((s) => s.loadPages);
    const { setPage } = useEditorNavigation();
    const [urlCopied, setUrlCopied] = useState(false);
    const [revisions, setRevisions] = useState<
        Array<{ id: string; saved_at: number; saved_at_iso: string }>
    >([]);
    const [revisionsLoading, setRevisionsLoading] = useState(false);
    const [selectedRevisionId, setSelectedRevisionId] = useState<string>("");
    const [restoring, setRestoring] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const [templates, setTemplates] = useState<Array<{ label: string; value: string }>>([]);
    const [newTemplateName, setNewTemplateName] = useState("");
    const [creatingTemplate, setCreatingTemplate] = useState(false);
    const isPreservedPage = (config.preservedPages ?? []).includes(
        (currentSlug ?? "").toLowerCase()
    );

    const publicUrl =
        currentSlug !== null && currentSlug !== ""
            ? api.getLiveUrl(currentSlug)
            : "";

    const copyPublicUrl = useCallback(async () => {
        if (!publicUrl || !navigator.clipboard?.writeText) return;
        try {
            await navigator.clipboard.writeText(publicUrl);
            setUrlCopied(true);
            window.setTimeout(() => setUrlCopied(false), 2000);
        } catch {
            /* ignore */
        }
    }, [publicUrl]);

    const handleChange = useCallback(
        (field: keyof PageMeta, value: string) => {
            editor.pages.updateMeta({ [field]: value });
        },
        [editor]
    );

    useEffect(() => {
        let cancelled = false;
        void api
            .getTemplates()
            .then((res) => {
                if (!cancelled) {
                    setTemplates(res.templates ?? []);
                }
            })
            .catch(() => {
                if (!cancelled) setTemplates([]);
            });
        return () => {
            cancelled = true;
        };
    }, []);

    useEffect(() => {
        if (!currentSlug) {
            setRevisions([]);
            setSelectedRevisionId("");
            return;
        }
        let cancelled = false;
        setRevisionsLoading(true);
        void api
            .getPageRevisions(currentSlug)
            .then((r) => {
                if (!cancelled) {
                    setRevisions(r.revisions ?? []);
                    setSelectedRevisionId("");
                }
            })
            .catch(() => {
                if (!cancelled) setRevisions([]);
            })
            .finally(() => {
                if (!cancelled) setRevisionsLoading(false);
            });
        return () => {
            cancelled = true;
        };
    }, [currentSlug]);

    const handleTemplateChange = useCallback(
        (value: string) => {
            editor.pages.updateTemplate(value);
        },
        [editor]
    );

    const handleCreateTemplate = useCallback(async () => {
        const normalized = newTemplateName.trim().toLowerCase().replace(/\.json$/i, "");
        if (!normalized) {
            toast.error("Template name is required.");
            return;
        }

        const pageSlug = normalized
            .replace(/[^a-z0-9/_-]+/g, "-")
            .replace(/[._]+/g, "-")
            .replace(/-+/g, "-")
            .replace(/^[-/]+|[-/]+$/g, "");

        if (!pageSlug) {
            toast.error("Template name is not valid for page creation.");
            return;
        }

        setCreatingTemplate(true);
        try {
            const res = await api.createTemplate({ name: normalized });
            const updatedTemplates = res.templates ?? [];
            setTemplates(updatedTemplates);
            setPage(pageSlug);
            await loadPage(pageSlug);
            editor.pages.updateTemplate(normalized);
            await editor.pages.save();
            await loadPages();
            setNewTemplateName("");
            toast.success(
                res.message ||
                    `Template "${normalized}" and page "${pageSlug}" created.`
            );
        } catch {
            toast.error("Could not create template.");
        } finally {
            setCreatingTemplate(false);
        }
    }, [editor, loadPage, loadPages, newTemplateName, setPage]);

    const handleRestoreRevision = useCallback(async () => {
        if (!currentSlug || !selectedRevisionId) return;
        const ok = window.confirm(
            "Replace the current page content with this saved revision? Unsaved changes in the editor will be lost after reload."
        );
        if (!ok) return;
        setRestoring(true);
        try {
            const res = await api.restorePageRevision(
                currentSlug,
                selectedRevisionId
            );
            await loadPage(currentSlug);
            toast.success(
                typeof res?.message === "string" && res.message !== ""
                    ? res.message
                    : "Revision restored."
            );
            const r = await api.getPageRevisions(currentSlug);
            setRevisions(r.revisions ?? []);
            setSelectedRevisionId("");
        } catch {
            toast.error("Could not restore this revision.");
        } finally {
            setRestoring(false);
        }
    }, [currentSlug, loadPage, selectedRevisionId]);

    const handleDeletePage = useCallback(async () => {
        if (!currentSlug || isPreservedPage) return;
        const ok = window.confirm(
            `Delete "${currentSlug}" page permanently? This will remove its JSON file, revision history, and database entry.`
        );
        if (!ok) return;

        setDeleting(true);
        try {
            const res = await api.deletePage(currentSlug);
            await loadPages();
            toast.success(
                typeof res?.message === "string" && res.message !== ""
                    ? res.message
                    : "Page deleted."
            );
            setPage("home");
        } catch {
            toast.error("Could not delete this page.");
        } finally {
            setDeleting(false);
        }
    }, [currentSlug, isPreservedPage, loadPages, setPage]);

    return (
        <div className="px-4 py-4 space-y-4">
            {currentSlug && (
                <div>
                    <label className="flex items-center gap-1.5 text-xs font-semibold text-gray-600 mb-1.5">
                        <History className="w-3.5 h-3.5" aria-hidden />
                        Revision history
                    </label>
                    {revisionsLoading ? (
                        <p className="text-xs text-gray-400">Loading…</p>
                    ) : revisions.length === 0 ? (
                        <p className="text-[11px] text-gray-400 leading-snug">
                            No snapshots yet. Each save keeps the previous file
                            (up to the limit in config).
                        </p>
                    ) : (
                        <div className="flex flex-col gap-2">
                            <select
                                value={selectedRevisionId}
                                onChange={(e) =>
                                    setSelectedRevisionId(e.target.value)
                                }
                                className="w-full px-3 py-2 text-xs border border-gray-200 rounded-lg bg-white"
                            >
                                <option value="">Select a revision…</option>
                                {revisions.map((rev) => (
                                    <option key={rev.id} value={rev.id}>
                                        {new Date(
                                            rev.saved_at * 1000
                                        ).toLocaleString()}{" "}
                                        ({rev.id.slice(0, 15)}…)
                                    </option>
                                ))}
                            </select>
                            <button
                                type="button"
                                disabled={
                                    !selectedRevisionId || restoring
                                }
                                onClick={() => void handleRestoreRevision()}
                                className="w-full px-3 py-2 text-xs font-medium rounded-lg border border-amber-200 bg-amber-50 text-amber-900 hover:bg-amber-100 disabled:opacity-40 disabled:cursor-not-allowed"
                            >
                                {restoring ? "Restoring…" : "Restore revision"}
                            </button>
                        </div>
                    )}
                </div>
            )}

            {publicUrl && (
                <div>
                    <label className="block text-xs font-semibold text-gray-600 mb-1.5">
                        Public URL
                    </label>
                    <div className="flex gap-1.5">
                        <input
                            type="text"
                            readOnly
                            value={publicUrl}
                            className="min-w-0 flex-1 px-3 py-2 text-xs border border-gray-200 rounded-lg bg-gray-50 text-gray-700 font-mono"
                        />
                        <button
                            type="button"
                            onClick={() => void copyPublicUrl()}
                            title="Copy URL"
                            className="shrink-0 px-2.5 py-2 rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-gray-50"
                        >
                            {urlCopied ? (
                                <Check className="w-4 h-4 text-emerald-600" />
                            ) : (
                                <Copy className="w-4 h-4" />
                            )}
                        </button>
                    </div>
                    <p className="mt-1 text-[11px] text-gray-400">
                        Visitor-facing address (no editor). Draft pages may
                        return 404 until published.
                    </p>
                </div>
            )}

            {currentSlug && !isPreservedPage && (
                <div className="pt-1">
                    <button
                        type="button"
                        onClick={() => void handleDeletePage()}
                        disabled={deleting}
                        className="w-full px-3 py-2 text-xs font-medium rounded-lg border border-red-200 bg-red-50 text-red-700 hover:bg-red-100 disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center justify-center gap-1.5"
                    >
                        <Trash2 className="w-3.5 h-3.5" aria-hidden />
                        {deleting ? "Deleting..." : "Delete page"}
                    </button>
                    <p className="mt-1 text-[11px] text-gray-400">
                        Removes this page from editor and site routes.
                    </p>
                </div>
            )}

            {/* Page Title */}
            <div>
                <label className="block text-xs font-semibold text-gray-600 mb-1.5">
                    Template
                </label>
                <select
                    value={pageTemplate || "page"}
                    onChange={(e) => handleTemplateChange(e.target.value)}
                    className="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-colors"
                >
                    {templates.length === 0 ? (
                        <option value="page">page</option>
                    ) : (
                        templates.map((tpl) => (
                            <option key={tpl.value} value={tpl.value}>
                                {tpl.value}
                            </option>
                        ))
                    )}
                </select>
                <p className="mt-1 text-[11px] text-gray-400">
                    Pick which JSON template this page should use.
                </p>
            </div>

            <div>
                <label className="block text-xs font-semibold text-gray-600 mb-1.5">
                    Create new template
                </label>
                <div className="flex gap-2">
                    <input
                        type="text"
                        value={newTemplateName}
                        onChange={(e) => setNewTemplateName(e.target.value)}
                        placeholder="e.g. page.alternate"
                        className="min-w-0 flex-1 px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-colors"
                    />
                    <button
                        type="button"
                        onClick={() => void handleCreateTemplate()}
                        disabled={creatingTemplate}
                        className="px-3 py-2 text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {creatingTemplate ? "Creating..." : "Create"}
                    </button>
                </div>
                <p className="mt-1 text-[11px] text-gray-400">
                    Creates `resources/views/templates/{`{name}`}.json` and selects it.
                </p>
            </div>

            {/* Page Title */}
            <div>
                <label className="block text-xs font-semibold text-gray-600 mb-1.5">
                    Page title
                </label>
                <input
                    type="text"
                    value={meta.title ?? ""}
                    onChange={(e) => handleChange("title", e.target.value)}
                    placeholder="Page title"
                    className="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-colors"
                />
                <p className="mt-1 text-[11px] text-gray-400">
                    The name used to identify this page.
                </p>
            </div>

            {/* Meta Title */}
            <div>
                <label className="block text-xs font-semibold text-gray-600 mb-1.5">
                    Meta title
                </label>
                <input
                    type="text"
                    value={meta.meta_title ?? ""}
                    onChange={(e) => handleChange("meta_title", e.target.value)}
                    placeholder="Page title for search engines"
                    className="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-colors"
                />
                <p className="mt-1 text-[11px] text-gray-400">
                    Displayed in browser tabs and search engine results.
                </p>
            </div>

            {/* Meta Description */}
            <div>
                <label className="block text-xs font-semibold text-gray-600 mb-1.5">
                    Meta description
                </label>
                <textarea
                    value={meta.meta_description ?? ""}
                    onChange={(e) =>
                        handleChange("meta_description", e.target.value)
                    }
                    placeholder="Brief description for search engines"
                    rows={3}
                    className="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white resize-none focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-colors"
                />
                <p className="mt-1 text-[11px] text-gray-400">
                    Recommended length: 150–160 characters.
                </p>
            </div>

            {/* Meta Keywords */}
            <div>
                <label className="block text-xs font-semibold text-gray-600 mb-1.5">
                    Meta keywords
                </label>
                <input
                    type="text"
                    value={meta.meta_keywords ?? ""}
                    onChange={(e) =>
                        handleChange("meta_keywords", e.target.value)
                    }
                    placeholder="keyword1, keyword2, keyword3"
                    className="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-colors"
                />
                <p className="mt-1 text-[11px] text-gray-400">
                    Comma-separated keywords for SEO.
                </p>
            </div>
        </div>
    );
}

export default memo(PageMetaPanel);
