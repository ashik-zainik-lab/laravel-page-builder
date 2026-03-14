import React from "react";
import { LogOut } from "lucide-react";

/**
 * Editor logo / branding mark displayed at the far-left of the header.
 *
 * SRP: Clickable exit button that navigates to the app URL from config.
 * The appUrl is configured in layout.blade.php via window.PageBuilder.appUrl
 */
export default function EditorLogo() {
    const appUrl = window.PageBuilder?.appUrl || "/";

    return (
        <button
            onClick={() => {
                window.location.href = appUrl;
            }}
            className="flex items-center justify-center w-9 h-9 rounded-lg select-none hover:bg-gray-100 transition-colors"
            title="Exit editor"
        >
            <LogOut size={16} className="text-gray-700 rotate-180" />
        </button>
    );
}
