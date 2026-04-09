import config from "../config";
import { del, get, post } from "./apiFetch";

/**
 * API service for communicating with the Laravel page builder backend.
 *
 * All methods use the `apiFetch` wrapper which handles CSRF tokens,
 * JSON headers, and consistent error handling automatically.
 */
const api = {
    /**
     * Fetch all pages.
     */
    async getPages() {
        return get<any[]>(`${config.baseUrl}/pages`);
    },

    /**
     * Fetch a single page by slug.
     */
    async getPage(slug: string) {
        return get<any>(`${config.baseUrl}/page/${slug}`);
    },

    /**
     * Delete a page by slug.
     */
    async deletePage(slug: string) {
        const enc = encodeURIComponent(slug);
        return del<{ message?: string }>(`${config.baseUrl}/page/${enc}`);
    },

    /**
     * List JSON revision snapshots for a page (newest first).
     */
    async getPageRevisions(slug: string) {
        const enc = encodeURIComponent(slug);
        return get<{
            revisions: Array<{
                id: string;
                saved_at: number;
                saved_at_iso: string;
            }>;
        }>(`${config.baseUrl}/page/${enc}/revisions`);
    },

    /**
     * Restore page JSON from a snapshot (disk only; reload editor after).
     */
    async restorePageRevision(slug: string, revisionId: string) {
        const encSlug = encodeURIComponent(slug);
        const encId = encodeURIComponent(revisionId);
        return post<{ message?: string }>(
            `${config.baseUrl}/page/${encSlug}/revisions/${encId}/restore`,
            {}
        );
    },

    /**
     * Render a block with given settings (live preview update).
     */
    async renderBlock(payload: Record<string, any>) {
        return post<{ html: string }>(
            `${config.baseUrl}/render-block`,
            payload
        );
    },

    /**
     * Render a section with given settings (live preview update).
     */
    async renderSection(payload: Record<string, any>) {
        return post<{ html: string }>(
            `${config.baseUrl}/render-section`,
            payload
        );
    },

    /**
     * Save a page (sections + meta).
     */
    async savePage(
        slug: string,
        data: any,
        template: string,
        meta?: any,
        themeSettings?: Record<string, any>,
        isActive = true
    ) {
        return post<any>(`${config.baseUrl}/save-page`, {
            slug,
            data,
            template,
            meta,
            theme_settings: themeSettings,
            is_active: isActive,
        });
    },

    async getTemplates() {
        return get<{ templates: Array<{ label: string; value: string }> }>(
            `${config.baseUrl}/templates`
        );
    },

    async createTemplate(payload: {
        name: string;
        layout?: string;
        wrapper?: string;
        force?: boolean;
    }) {
        return post<{ message?: string; templates?: Array<{ label: string; value: string }> }>(
            `${config.baseUrl}/templates`,
            payload
        );
    },

    /**
     * Get theme settings (schema + values).
     */
    async getThemeSettings() {
        return get<{ schema: any[]; values: Record<string, any> }>(
            `${config.baseUrl}/theme-settings`
        );
    },

    /**
     * Save theme settings values.
     */
    async saveThemeSettings(values: Record<string, any>) {
        return post<any>(`${config.baseUrl}/theme-settings`, { values });
    },

    /**
     * Get the preview URL for a page.
     *
     * Uses the real page URL with ?pb-editor=1 query parameter
     * so the preview renders through the actual site layout.
     */
    getPreviewUrl(slug: string): string {
        const params = new URLSearchParams({ "pb-editor": "1" });
        // Home page is served at "/", other pages at "/{slug}"
        const path = slug === "home" ? "/" : `/${slug}`;
        return `${config.appUrl}${path}?${params.toString()}`;
    },

    /**
     * Public page URL (no editor query) — opens how visitors see the page.
     */
    getLiveUrl(slug: string): string {
        const path = slug === "home" ? "/" : `/${slug}`;
        return `${config.appUrl}${path}`;
    },
};

export default api;
