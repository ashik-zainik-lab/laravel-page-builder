import React, { memo } from "react";
import { X } from "lucide-react";
import { cn } from "@/lib/utils";
import { useToastStore } from "@/core/toastStore";

/**
 * Fixed stack of transient notifications (save feedback, errors).
 */
function ToastViewport() {
    const toasts = useToastStore((s) => s.toasts);
    const dismiss = useToastStore((s) => s.dismiss);

    if (toasts.length === 0) return null;

    return (
        <div
            className="fixed bottom-20 md:bottom-6 right-4 z-[100] flex flex-col gap-2 max-w-[min(100vw-2rem,360px)] pointer-events-none"
            aria-live="polite"
        >
            {toasts.map((t) => (
                <div
                    key={t.id}
                    className={cn(
                        "pointer-events-auto flex items-start gap-2 rounded-lg border px-3 py-2.5 text-sm shadow-lg transition-opacity duration-200",
                        t.variant === "success" &&
                            "bg-emerald-50 border-emerald-200 text-emerald-950",
                        t.variant === "error" &&
                            "bg-red-50 border-red-200 text-red-950",
                        t.variant === "info" &&
                            "bg-white border-gray-200 text-gray-900",
                    )}
                    role="status"
                >
                    <p className="flex-1 leading-snug">{t.message}</p>
                    <button
                        type="button"
                        onClick={() => dismiss(t.id)}
                        className="shrink-0 rounded p-0.5 text-current opacity-60 hover:opacity-100"
                        aria-label="Dismiss"
                    >
                        <X className="w-4 h-4" />
                    </button>
                </div>
            ))}
        </div>
    );
}

export default memo(ToastViewport);
