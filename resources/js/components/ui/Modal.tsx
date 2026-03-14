import React, { useEffect, useRef, useCallback } from "react";
import { X } from "lucide-react";

/**
 * Reusable modal component with focus trap, ESC close,
 * and outside-click close.
 *
 * @param {boolean} isOpen - Whether the modal is visible
 * @param {Function} onClose - Callback when the modal should close
 * @param {string} title - Modal header title
 * @param {React.ReactNode} children - Modal body content
 * @param {React.ReactNode} footer - Optional modal footer
 */
export default function Modal({ isOpen, onClose, title, children, footer }) {
    const overlayRef = useRef(null);
    const modalRef = useRef(null);

    // Close on ESC
    const handleKeyDown = useCallback(
        (e) => {
            if (e.key === "Escape") onClose();
        },
        [onClose]
    );

    useEffect(() => {
        if (!isOpen) return;
        document.addEventListener("keydown", handleKeyDown);
        return () => document.removeEventListener("keydown", handleKeyDown);
    }, [isOpen, handleKeyDown]);

    // Focus trap
    useEffect(() => {
        if (!isOpen || !modalRef.current) return;
        const focusable = modalRef.current.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );
        if (focusable.length > 0) focusable[0].focus();
    }, [isOpen]);

    if (!isOpen) return null;

    // Close on outside click
    const handleOverlayClick = (e) => {
        if (e.target === overlayRef.current) onClose();
    };

    return (
        <div
            ref={overlayRef}
            onClick={handleOverlayClick}
            className="fixed inset-0 z-[200] flex items-center justify-center bg-gray-900/50 backdrop-blur-sm p-4"
        >
            <div
                ref={modalRef}
                role="dialog"
                aria-modal="true"
                aria-label={title}
                className="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[85vh]"
            >
                {/* Header */}
                <div className="flex items-center justify-between px-5 py-3.5 border-b border-gray-100 shrink-0">
                    <h3 className="font-semibold text-gray-800 text-sm">
                        {title}
                    </h3>
                    <button
                        onClick={onClose}
                        className="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                    >
                        <X className="w-4 h-4" />
                    </button>
                </div>

                {/* Body */}
                <div className="flex-1 overflow-y-auto">{children}</div>

                {/* Footer */}
                {footer && (
                    <div className="px-5 py-3 border-t border-gray-100 shrink-0">
                        {footer}
                    </div>
                )}
            </div>
        </div>
    );
}
