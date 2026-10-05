import { Link, usePage } from '@inertiajs/react';
import React, { useEffect, useRef, useState } from 'react';
import logoWhite from '../../assets/images/logo-only-white.png';
import Offcanvas from '../ui/offcanvas';
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
    const [isMenuOpen, setIsMenuOpen] = useState(false);
    const headerRef = useRef<HTMLDivElement>(null);

    // Scroll Effect (Auto-Hide Header)
    useEffect(() => {
        let lastScrollY = typeof window !== 'undefined' ? window.scrollY : 0;
        let ticking = false;
        const HIDE_THRESHOLD = 50;

        const handleScroll = () => {
            if (!ticking) {
                window.requestAnimationFrame(() => {
                    const currentScrollY = Math.max(0, window.scrollY);
                    setIsScrolled(currentScrollY > 20);

                    if (currentScrollY <= HIDE_THRESHOLD) {
                        setIsVisible(true);
                    } else if (Math.abs(currentScrollY - lastScrollY) > 8) {
                        setIsVisible(currentScrollY < lastScrollY);
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
            className={`smart-header sticky-top ${
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
                    <span className="fs-5 fw-bold text-truncate">
                        {displayName}
                    </span>
                </Link>

                {/* Desktop Buttons (Visible on lg and up) */}
                <div className="d-none d-lg-flex align-items-center gap-2 flex-shrink-0">
                    <a
                        className="btn btn-outline-secondary rounded-pill px-3"
                        href="https://afrizahanif.com"
                        target="_blank"
                        rel="noreferrer"
                        title="Back to Home"
                        aria-label="Back to Home"
                    >
                        <i className="bi bi-arrow-left me-1"></i>
                        Back to Home
                    </a>
                    <a
                        className="btn btn-outline-success d-flex align-items-center rounded-pill px-3"
                        href="/up"
                        target="_blank"
                        rel="noreferrer"
                        title="Check Laravel Health Status"
                        aria-label="Check Laravel Health Status"
                    >
                        <span
                            className="spinner-grow spinner-grow-sm text-success me-2"
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
                        aria-label="Explore API"
                    >
                        <i className="bi bi-box-arrow-up-right me-1"></i>
                        Explore API
                    </a>
                </div>

                {/* Mobile & Tablet Toggle Button (Visible below lg) */}
                <button
                    type="button"
                    className="btn btn-outline-secondary d-lg-none d-inline-flex align-items-center justify-content-center p-2 rounded-circle"
                    onClick={() => setIsMenuOpen(true)}
                    aria-label="Open Navigation Menu"
                    title="Open Navigation Menu"
                    style={{ width: '40px', height: '40px' }}
                >
                    <i className="bi bi-list fs-5"></i>
                </button>
            </header>

            {/* Mobile / Tablet Drawer */}
            <Offcanvas
                id="header-mobile-menu"
                show={isMenuOpen}
                onClose={() => setIsMenuOpen(false)}
                bodyClassName="d-flex flex-column"
                title={
                    <div className="d-flex align-items-center">
                        <img
                            src={logoWhite}
                            alt="Logo"
                            width={28}
                            height={28}
                            className="me-2 flex-shrink-0 object-fit-contain"
                        />
                        <span className="fs-6 fw-bold text-truncate">
                            {displayName}
                        </span>
                    </div>
                }
                placement="end"
            >
                <div className="d-flex flex-column gap-3 py-2">
                    <a
                        className="btn btn-outline-secondary d-flex align-items-center justify-content-between text-start p-3 rounded-3"
                        href="https://afrizahanif.com"
                        target="_blank"
                        rel="noreferrer"
                        onClick={() => setIsMenuOpen(false)}
                    >
                        <span className="d-flex align-items-center">
                            <i className="bi bi-arrow-left fs-5 me-3"></i>
                            <span>
                                <div className="fw-semibold">Back to Home</div>
                                <div className="text-secondary small">
                                    Return to main portfolio
                                </div>
                            </span>
                        </span>
                        <i className="bi bi-box-arrow-up-right small text-muted"></i>
                    </a>

                    <a
                        className="btn btn-outline-success d-flex align-items-center justify-content-between text-start p-3 rounded-3"
                        href="/up"
                        target="_blank"
                        rel="noreferrer"
                        onClick={() => setIsMenuOpen(false)}
                    >
                        <span className="d-flex align-items-center">
                            <span
                                className="spinner-grow spinner-grow-sm text-success me-3"
                                style={{ width: '0.85rem', height: '0.85rem' }}
                            ></span>
                            <span>
                                <div className="fw-semibold">
                                    Check Health Status
                                </div>
                                <div className="text-secondary small">
                                    Verify application uptime & services
                                </div>
                            </span>
                        </span>
                        <i className="bi bi-heart-pulse text-success fs-5"></i>
                    </a>

                    <a
                        className="btn btn-primary d-flex align-items-center justify-content-between text-start p-3 rounded-3"
                        href="/api/profiles"
                        target="_blank"
                        rel="noreferrer"
                        onClick={() => setIsMenuOpen(false)}
                    >
                        <span className="d-flex align-items-center">
                            <i className="bi bi-code-slash fs-5 me-3"></i>
                            <span>
                                <div className="fw-semibold text-white">
                                    Explore API
                                </div>
                                <div className="text-white-50 small">
                                    Access endpoints & schemas
                                </div>
                            </span>
                        </span>
                        <i className="bi bi-arrow-right fs-5 text-white"></i>
                    </a>
                </div>

                <div className="mt-auto pt-3 border-top text-center text-secondary small">
                    {appName}
                </div>
            </Offcanvas>
        </div>
    );
}
