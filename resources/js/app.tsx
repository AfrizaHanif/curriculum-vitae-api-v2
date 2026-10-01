import { createInertiaApp } from "@inertiajs/react";
if (typeof window !== "undefined") {
    void import("bootstrap");
}

const appName = import.meta.env.VITE_APP_NAME || "CV API";

void createInertiaApp({
    title: (title) => {
        if (!title) return appName;
        return title.includes(appName) ? title : `${title} - ${appName}`;
    },
    progress: {
        color: "#4B5563",
    },
});
