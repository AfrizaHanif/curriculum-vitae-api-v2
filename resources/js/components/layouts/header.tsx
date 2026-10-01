import { Link, usePage } from '@inertiajs/react';
import React, { useEffect, useRef, useState } from 'react';
import logoWhite from '../../assets/images/logo-only-white.png';
import './header.css';

interface HeaderProps {
    appName?: string;
    fullName?: string;
}

export default function Header({
    appName = 'Curriculum Vitae API',
    fullName: propFullName,
}: HeaderProps) {
    const page = usePage();
    const displayName = propFullName ?? page.props.profile?.fullname ?? appName;
    const [isVisible, setIsVisible] = useState(true);
    const [isScrolled, setIsScrolled] = useState(false);
    const headerRef = useRef<HTMLDivElement>(null);

    // Scroll Effect (Auto-Hide Header)
    useEffect(() => {
        let lastScrollY = window.scrollY;
        let ticking = false;
        const HIDE_THRESHOLD = 50;

        const handleScroll = () => {
            if (!ticking) {
                window.requestAnimationFrame(() => {
                    const currentScrollY = window.scrollY;
                    setIsScrolled(currentScrollY > 20);

                    if (currentScrollY < HIDE_THRESHOLD) {
                        setIsVisible(true);
                    } else if (currentScrollY < lastScrollY) {
                        setIsVisible(true);
                    } else {
                        setIsVisible(false);
                    }

                    lastScrollY = currentScrollY;
                    ticking = false;
                });
                ticking = true;
            }
        };

        window.addEventListener('scroll', handleScroll, { passive: true });
        return () => window.removeEventListener('scroll', handleScroll);
    }, []);

    return (
        <div
            ref={headerRef}
            className={`smart-header fixed-top ${
                isVisible ? 'header-visible' : 'header-hidden'
            } ${isScrolled ? 'shadow-sm scrolled' : ''}`}
        >
            <header className="container d-flex align-items-center justify-content-between py-3">
                <Link
                    href="/"
                    className="d-flex align-items-center link-body-emphasis text-decoration-none min-w-0 me-2"
                >
                    <img
                        src={logoWhite}
                        alt="Logo"
                        width={36}
                        height={36}
                        className="me-2 flex-shrink-0 object-fit-contain"
                    />
                    <span className="fs-5 fs-md-4 fw-bold text-truncate d-none d-sm-inline">
                        {displayName}
                    </span>
                </Link>
                <div className="d-flex align-items-center gap-2">
                    <a
                        className="btn btn-outline-secondary rounded-pill px-3"
                        href="https://afrizahanif.com"
                        target="_blank"
                        rel="noreferrer"
                        title="Back to Home"
                    >
                        <i className="bi bi-arrow-left me-1"></i>
                        Back to Home
                    </a>
                    <a
                        className="btn btn-outline-success d-flex align-items-center gap-2 rounded-pill px-3"
                        href="/up"
                        target="_blank"
                        rel="noreferrer"
                        title="Check Laravel Health Status"
                    >
                        <span
                            className="spinner-grow spinner-grow-sm text-success"
                            style={{ width: '0.7rem', height: '0.7rem' }}
                        ></span>
                        Check Health Status
                    </a>
                    <a
                        className="btn btn-primary rounded-pill px-3"
                        href="/api/profiles"
                        target="_blank"
                        rel="noreferrer"
                        title="Explore API"
                    >
                        <i className="bi bi-box-arrow-up-right me-1"></i>
                        Explore API
                    </a>
                </div>
            </header>
        </div>
    );
}
