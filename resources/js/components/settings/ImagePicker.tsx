import React, { useState } from "react";
import { Image, X } from "lucide-react";
import AssetModal from "@/components/assets/AssetModal";

/**
 * Image picker setting component.
 *
 * Used inside section/block settings to select an image
 * from the asset manager.
 *
 * @param {string} value - Current image URL
 * @param {Function} onChange - Called with new URL when asset is selected
 * @param {string} label - Optional label text
 * @param {string} info - Optional info text
 *
 * @example
 * <ImagePicker
 *   value={imageUrl}
 *   onChange={(url) => updateSetting("image", url)}
 * />
 */
export default function ImagePicker({ value, onChange, label, info }) {
    const [modalOpen, setModalOpen] = useState(false);

    const handleSelect = (asset) => {
        onChange(asset.url);
    };

    const handleRemove = (e) => {
        e.stopPropagation();
        onChange("");
    };

    return (
        <div className="mb-4">
            {label && (
                <label className="block text-[11px] font-semibold text-gray-500 mb-1.5 tracking-wide">
                    {label}
                </label>
            )}

            {value ? (
                /* ── Has image ── */
                <div className="group relative rounded-lg overflow-hidden border border-gray-200 bg-gray-50">
                    <img
                        src={value}
                        alt=""
                        className="w-full h-28 object-cover"
                        onError={(e) => {
                            // e.target.style.display = "none";
                        }}
                    />
                    <div className="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-colors flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100">
                        <button
                            onClick={() => setModalOpen(true)}
                            className="px-2.5 py-1 bg-white rounded-md text-xs font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition-colors"
                        >
                            Change
                        </button>
                        <button
                            onClick={handleRemove}
                            className="p-1.5 bg-white rounded-md text-gray-500 shadow-sm hover:bg-red-50 hover:text-red-500 transition-colors"
                            title="Remove image"
                        >
                            <X className="w-3.5 h-3.5" />
                        </button>
                    </div>
                </div>
            ) : (
                /* ── No image ── */
                <button
                    onClick={() => setModalOpen(true)}
                    className="w-full flex flex-col items-center justify-center gap-1.5 py-5 border-2 border-dashed border-gray-200 rounded-lg hover:border-gray-300 hover:bg-gray-50/50 transition-colors cursor-pointer"
                >
                    <Image className="w-5 h-5 text-gray-400" />
                    <span className="text-xs text-gray-500 font-medium">
                        Select image
                    </span>
                </button>
            )}

            {info && (
                <p className="text-[10px] text-gray-400 mt-1 leading-relaxed">
                    {info}
                </p>
            )}

            <AssetModal
                isOpen={modalOpen}
                onClose={() => setModalOpen(false)}
                onSelect={handleSelect}
            />
        </div>
    );
}
