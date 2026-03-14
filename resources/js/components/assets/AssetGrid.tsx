import React from "react";
import type { Asset } from "@/types/asset";
import { Loader2, Image as ImageIcon, Check } from "lucide-react";
import { cn } from "@/lib/utils";

/**
 * Grid of asset thumbnails.
 *
 * @param assets - Array of Asset objects
 * @param selectedId - Currently selected asset ID
 * @param onSelect - Called with asset when clicked
 * @param loading - Whether assets are loading
 */
export default function AssetGrid({
    assets = [],
    selectedId = null,
    onSelect,
    loading = false,
}: {
    assets?: Asset[];
    selectedId?: string | null;
    onSelect: (asset: Asset) => void;
    loading?: boolean;
}) {
    if (loading) {
        return (
            <div className="flex items-center justify-center py-12">
                <div className="flex items-center gap-2 text-xs text-gray-400">
                    <Loader2 className="w-4 h-4 animate-spin" />
                    Loading assets…
                </div>
            </div>
        );
    }

    if (assets.length === 0) {
        return (
            <div className="flex flex-col items-center justify-center py-12 text-gray-400">
                <ImageIcon className="w-8 h-8 mb-2" strokeWidth={1.5} />
                <span className="text-xs">No assets found</span>
            </div>
        );
    }

    return (
        <div className="grid grid-cols-3 sm:grid-cols-4 gap-2">
            {assets.map((asset) => {
                const isSelected = asset.id === selectedId;
                return (
                    <button
                        key={asset.id}
                        onClick={() => onSelect(asset)}
                        className={cn(
                            "group relative aspect-square rounded-lg overflow-hidden border-2 transition-all cursor-pointer",
                            isSelected
                                ? "border-blue-500 ring-2 ring-blue-200"
                                : "border-transparent hover:border-gray-300"
                        )}
                    >
                        <img
                            src={asset.thumbnail || asset.url}
                            alt={asset.name}
                            className="w-full h-full object-cover"
                            loading="lazy"
                        />

                        {/* Hover overlay with name */}
                        <div className="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-colors flex items-end">
                            <div className="w-full px-1.5 py-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <p className="text-[10px] text-white truncate">
                                    {asset.name}
                                </p>
                            </div>
                        </div>

                        {/* Selected indicator */}
                        {isSelected && (
                            <div className="absolute top-1 left-1 w-5 h-5 rounded-full bg-blue-500 flex items-center justify-center">
                                <Check
                                    className="w-3 h-3 text-white"
                                    strokeWidth={3}
                                />
                            </div>
                        )}
                    </button>
                );
            })}
        </div>
    );
}
