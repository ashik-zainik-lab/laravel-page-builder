import { create } from "zustand";

export type ToastVariant = "success" | "error" | "info";

export interface ToastItem {
    id: number;
    message: string;
    variant: ToastVariant;
}

interface ToastState {
    toasts: ToastItem[];
    show: (message: string, variant?: ToastVariant) => void;
    dismiss: (id: number) => void;
}

let idSeq = 0;
const defaultDurationMs = 4200;

export const useToastStore = create<ToastState>((set, get) => ({
    toasts: [],
    show: (message, variant = "info") => {
        const id = ++idSeq;
        set((s) => ({ toasts: [...s.toasts, { id, message, variant }] }));
        window.setTimeout(() => {
            get().dismiss(id);
        }, defaultDurationMs);
    },
    dismiss: (id) =>
        set((s) => ({ toasts: s.toasts.filter((t) => t.id !== id) })),
}));

export const toast = {
    success: (message: string): void =>
        useToastStore.getState().show(message, "success"),
    error: (message: string): void =>
        useToastStore.getState().show(message, "error"),
    info: (message: string): void =>
        useToastStore.getState().show(message, "info"),
};
