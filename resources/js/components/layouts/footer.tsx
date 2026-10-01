import type { SocialItem } from "@/types";
import { usePage } from "@inertiajs/react";
import logoWhite from "../../assets/images/logo-only-white.png";
import "./footer.css";

interface FooterProps {
    appName?: string;
    fullName?: string;
    laravelVersion?: string;
    phpVersion?: string;
    socials?: SocialItem[];
}

export default function Footer({
    appName = "Curriculum Vitae API",
    fullName: propFullName,
    laravelVersion,
    phpVersion,
    socials: propSocials,
}: FooterProps) {
    const page = usePage();
    const displayName = propFullName ?? page.props.profile?.fullname ?? appName;
    const pageSocials = (page.props.socials as SocialItem[] | undefined) ?? [];
    const socials = propSocials ?? pageSocials;

    const getIconClass = (icon: string): string => {
        if (icon.startsWith("bi-") || icon.startsWith("bi ")) {
            return icon;
        }
        return `bi-${icon}`;
    };

    return (
        <div className="glass-footer border-top mt-auto">
            <footer className="container py-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 text-center text-md-start">
                <div className="d-flex align-items-center justify-content-center justify-content-md-start">
                    <a
                        href="/"
                        className="me-2 text-body-secondary text-decoration-none lh-1"
                        aria-label={displayName}
                    >
                        <img
                            src={logoWhite}
                            alt="Logo"
                            width={24}
                            height={24}
                            className="object-fit-contain"
                            loading="lazy"
                        />
                    </a>
                    <span className="text-body-secondary small">
                        &copy; {new Date().getFullYear()} {displayName}. All
                        rights reserved. (
                        {(laravelVersion || phpVersion) && (
                            <div className="d-inline-flex gap-2">
                                {laravelVersion && (
                                    <span>Laravel v{laravelVersion}</span>
                                )}
                                {laravelVersion && phpVersion && (
                                    <span>&bull;</span>
                                )}
                                {phpVersion && <span>PHP v{phpVersion}</span>}
                                <span>&bull;</span>
                                <a
                                    href="/up"
                                    className="text-decoration-none text-body-secondary d-inline-flex align-items-center gap-1"
                                    target="_blank"
                                    rel="noreferrer"
                                >
                                    <i className="bi bi-heart-pulse text-success"></i>
                                    <span>Status</span>
                                </a>
                            </div>
                        )}
                        )
                    </span>
                </div>

                {/* {(laravelVersion || phpVersion) && (
                    <div className="d-flex align-items-center justify-content-center gap-2 text-body-secondary small">
                        {laravelVersion && <span>Laravel v{laravelVersion}</span>}
                        {laravelVersion && phpVersion && <span>&bull;</span>}
                        {phpVersion && <span>PHP v{phpVersion}</span>}
                        <span>&bull;</span>
                        <a
                            href="/up"
                            className="text-decoration-none text-body-secondary d-inline-flex align-items-center gap-1"
                            target="_blank"
                            rel="noreferrer"
                        >
                            <i className="bi bi-heart-pulse text-success"></i>
                            <span>Status</span>
                        </a>
                    </div>
                )} */}

                {socials.length > 0 && (
                    <ul className="nav justify-content-center justify-content-md-end list-unstyled d-flex mb-0">
                        {socials.map((social) => (
                            <li key={social.id} className="ms-3">
                                <a
                                    className="text-body-secondary"
                                    href={social.url}
                                    target="_blank"
                                    rel="noreferrer"
                                    aria-label={social.name}
                                    title={social.name}
                                >
                                    <i
                                        className={`bi ${getIconClass(social.icon)} fs-5`}
                                    ></i>
                                </a>
                            </li>
                        ))}
                    </ul>
                )}
            </footer>
        </div>
    );
}
