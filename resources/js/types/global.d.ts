import type { Auth } from '@/types/auth';
import type { ProfileSummary } from '@/types/profile';
import type { SocialItem } from '@/types/social';

declare module 'react' {
    interface InputHTMLAttributes<T> {
        passwordrules?: string;
    }
}

declare module '@inertiajs/core' {
    export interface InertiaConfig {
        sharedPageProps: {
            name: string;
            auth: Auth;
            profile: ProfileSummary | null;
            socials: SocialItem[];
            sidebarOpen: boolean;
            [key: string]: unknown;
        };
    }
}
