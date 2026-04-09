import {
    BlockData,
    Page,
    SectionData,
    TemplateOption,
    ThemeSettingsData,
} from "./types/page-builder";
import type { AssetProvider } from "./types/asset";
import laravelAssetProvider from "./services/laravelAssetProvider";

// Define the shape of our global configuration
export interface PageBuilderConfig {
    baseUrl: string;
    appUrl: string;
    /** Lowercased slugs that cannot be unpublished (e.g. home). */
    preservedPages?: string[];
    pages: Page[];
    sections: Record<string, SectionData>;
    blocks: Record<string, BlockData>;
    templates?: TemplateOption[];
    themeSettings: ThemeSettingsData;
    fields: Record<
        string,
        | {
              type: "external";
              fetchList: () => Promise<
                  Array<{
                      label: string | number;
                      value: string | number;
                  }>
              >;
          }
        | ((args: {
              setting: any;
              value: any;
              onChange: (val: any) => void;
              container: HTMLElement;
          }) => void | string | HTMLElement)
    >;
    [key: string]: any;
}

/**
 * Asset and editor service configuration.
 *
 * Supports provider injection so future providers
 * (S3, Cloudinary, Unsplash) can be swapped without
 * changing any UI code.
 */
export interface EditorConfig {
    assets?: {
        provider?: AssetProvider;
    };
}

// Default configuration fallback
const config: PageBuilderConfig = {
    baseUrl: "/pagebuilder",
    appUrl: "/",
    pages: [],
    sections: {},
    blocks: {},
    templates: [],
    themeSettings: { schema: [], values: {} },
    fields: {},
};

/**
 * Default editor configuration (uses the Laravel asset provider).
 * Passed to createEditor() when no overrides are supplied.
 */
export const defaultConfig: EditorConfig = {
    assets: {
        provider: laravelAssetProvider,
    },
};

/**
 * Update the global configuration.
 * Call this during PageBuilder.init() to inject settings.
 */
export function setConfig(newConfig: Partial<PageBuilderConfig>) {
    Object.assign(config, newConfig);
}

export default config;
