import Footer from '@/components/layouts/footer';
import Header from '@/components/layouts/header';
import { Head } from '@inertiajs/react';
import { useMemo, useState } from 'react';

interface WelcomeProps {
    appName?: string;
    laravelVersion?: string;
    phpVersion?: string;
}

interface EndpointItem {
    name: string;
    path: string;
    description: string;
    category: 'Profile' | 'Career' | 'Works' | 'Content' | 'Auth';
    methods: ('GET' | 'POST' | 'PUT' | 'DELETE')[];
    access: 'public' | 'auth' | 'public-read';
    hasRestore?: boolean;
}

const ENDPOINTS: EndpointItem[] = [
    {
        name: 'Authentication Login',
        path: '/api/login',
        description:
            'Authenticate and receive a Sanctum API bearer token (rate limited 6 req/min).',
        category: 'Auth',
        methods: ['POST'],
        access: 'public',
    },
    // {
    //     name: "Authenticated User",
    //     path: "/api/user",
    //     description:
    //         "Fetch current authenticated user data with attached profile relations.",
    //     category: "Auth",
    //     methods: ["GET"],
    //     access: "auth",
    // },
    {
        name: 'Profiles',
        path: '/api/profiles',
        description:
            'Core biography, personal statement, contact metadata, and headline information.',
        category: 'Profile',
        methods: ['GET', 'PUT'],
        access: 'public-read',
    },
    {
        name: 'Skills',
        path: '/api/skills',
        description:
            'Technical and soft skills taxonomy, proficiency metrics, and category tags.',
        category: 'Career',
        methods: ['GET', 'POST', 'PUT', 'DELETE'],
        access: 'public-read',
        hasRestore: true,
    },
    {
        name: 'Experiences',
        path: '/api/experiences',
        description:
            'Professional employment timeline, roles, responsibilities, and achievements.',
        category: 'Career',
        methods: ['GET', 'POST', 'PUT', 'DELETE'],
        access: 'public-read',
        hasRestore: true,
    },
    {
        name: 'Education',
        path: '/api/educations',
        description:
            'Academic background, degrees, certifications, and educational milestones.',
        category: 'Career',
        methods: ['GET', 'POST', 'PUT', 'DELETE'],
        access: 'public-read',
        hasRestore: true,
    },
    {
        name: 'Expertises',
        path: '/api/expertises',
        description:
            'Domain mastery areas, specialized consulting fields, and core proficiencies.',
        category: 'Career',
        methods: ['GET', 'POST', 'PUT', 'DELETE'],
        access: 'public-read',
        hasRestore: true,
    },
    {
        name: 'Certificates',
        path: '/api/certificates',
        description:
            'Official credentials, issuing organizations, credentials URLs, and issue dates.',
        category: 'Career',
        methods: ['GET', 'POST', 'PUT', 'DELETE'],
        access: 'public-read',
        hasRestore: true,
    },
    {
        name: 'Projects',
        path: '/api/projects',
        description:
            'Showcased applications, client deliverables, tech stacks, and live links.',
        category: 'Works',
        methods: ['GET', 'POST', 'PUT', 'DELETE'],
        access: 'public-read',
        hasRestore: true,
    },
    {
        name: 'Portfolios',
        path: '/api/portfolios',
        description:
            'Curated creative and engineering highlights with media assets and screenshots.',
        category: 'Works',
        methods: ['GET', 'POST', 'PUT', 'DELETE'],
        access: 'public-read',
        hasRestore: true,
    },
    {
        name: 'Case Studies',
        path: '/api/case-studies',
        description:
            'In-depth problem-solution architectural breakdowns and impact metrics.',
        category: 'Works',
        methods: ['GET', 'POST', 'PUT', 'DELETE'],
        access: 'public-read',
        hasRestore: true,
    },
    {
        name: 'Posts / Articles',
        path: '/api/posts',
        description:
            'Technical blog writeups, engineering notes, and published thought pieces.',
        category: 'Content',
        methods: ['GET', 'POST', 'PUT', 'DELETE'],
        access: 'public-read',
        hasRestore: true,
    },
    {
        name: 'Testimonials',
        path: '/api/testimonials',
        description:
            'Client endorsements, colleague feedback, and verified recommendations.',
        category: 'Content',
        methods: ['GET', 'POST', 'PUT', 'DELETE'],
        access: 'public-read',
        hasRestore: true,
    },
    {
        name: 'Social Links',
        path: '/api/socials',
        description:
            'Social networking handles, developer links (GitHub, LinkedIn), and URLs.',
        category: 'Profile',
        methods: ['GET', 'POST', 'PUT', 'DELETE'],
        access: 'public-read',
        hasRestore: true,
    },
    {
        name: 'Workstation Setup',
        path: '/api/setups',
        description:
            'Hardware gear, peripherals, development tooling, and workstation specs.',
        category: 'Profile',
        methods: ['GET', 'POST', 'PUT', 'DELETE'],
        access: 'public-read',
        hasRestore: true,
    },
    {
        name: 'Hobbies & Interests',
        path: '/api/hobbies',
        description:
            'Personal activities, creative pursuits, and extracurricular interests.',
        category: 'Profile',
        methods: ['GET', 'POST', 'PUT', 'DELETE'],
        access: 'public-read',
        hasRestore: true,
    },
    {
        name: 'System Features',
        path: '/api/features',
        description:
            'Feature flags, highlighted portfolio sections, and configuration items.',
        category: 'Content',
        methods: ['GET', 'POST', 'PUT', 'DELETE'],
        access: 'public-read',
        hasRestore: true,
    },
];

export default function Welcome({
    appName = 'Curriculum Vitae API',
    laravelVersion = '11.x',
    phpVersion = '8.3',
}: WelcomeProps) {
    const [searchQuery, setSearchQuery] = useState('');
    const [selectedCategory, setSelectedCategory] = useState<string>('All');
    const [copiedSnippet, setCopiedSnippet] = useState<string | null>(null);

    const categories = ['All', 'Auth', 'Profile', 'Career', 'Works', 'Content'];

    const filteredEndpoints = useMemo(() => {
        return ENDPOINTS.filter((endpoint) => {
            const matchesCat =
                selectedCategory === 'All' ||
                endpoint.category === selectedCategory;
            const matchesSearch =
                endpoint.path
                    .toLowerCase()
                    .includes(searchQuery.toLowerCase()) ||
                endpoint.name
                    .toLowerCase()
                    .includes(searchQuery.toLowerCase()) ||
                endpoint.description
                    .toLowerCase()
                    .includes(searchQuery.toLowerCase());
            return matchesCat && matchesSearch;
        });
    }, [searchQuery, selectedCategory]);

    const handleCopy = (text: string, id: string) => {
        void navigator.clipboard.writeText(text);
        setCopiedSnippet(id);
        setTimeout(() => setCopiedSnippet(null), 2000);
    };

    const getOrigin = () => {
        if (typeof window !== 'undefined') {
            return window.location.origin;
        }
        return 'https://api.yourdomain.com';
    };

    return (
        <>
            <Head title={`CV API`} />

            <div
                className="min-vh-100 bg-light d-flex flex-column text-body"
                style={{ paddingTop: '75px' }}
            >
                {/* Top Navigation */}
                <Header appName={appName} />

                {/* Hero Section */}
                <header className="bg-white border-bottom py-5">
                    <div className="container">
                        <div className="row align-items-center g-4">
                            <div className="col-lg-7">
                                <div className="d-inline-flex align-items-center gap-2 px-3 py-1 mb-3 rounded-pill bg-primary-subtle border border-primary-subtle text-primary fw-medium small">
                                    <i className="bi bi-check-circle-fill"></i>
                                    <span>
                                        Production-Ready REST Backend &bull;
                                        Laravel {laravelVersion}
                                    </span>
                                </div>
                                <h1 className="display-5 fw-bold text-dark mb-3">
                                    Curriculum Vitae &amp; Portfolio API
                                </h1>
                                <p className="lead text-muted mb-4">
                                    A structured, reliable RESTful API engine
                                    providing structured data for developer
                                    portfolios, work experience, technical
                                    skills, projects, and career credentials.
                                </p>

                                <div className="d-flex flex-wrap align-items-center gap-2">
                                    <div className="input-group input-group-sm w-auto shadow-sm">
                                        <span className="input-group-text bg-light text-muted border-end-0 font-monospace">
                                            Base URL
                                        </span>
                                        <input
                                            type="text"
                                            readOnly
                                            value={`${getOrigin()}/api`}
                                            className="form-control bg-light font-monospace"
                                            style={{ minWidth: 230 }}
                                        />
                                        <button
                                            type="button"
                                            className="btn btn-primary"
                                            onClick={() =>
                                                handleCopy(
                                                    `${getOrigin()}/api`,
                                                    'base-url',
                                                )
                                            }
                                        >
                                            {copiedSnippet === 'base-url' ? (
                                                <>
                                                    <i className="bi bi-check-lg me-1"></i>{' '}
                                                    Copied
                                                </>
                                            ) : (
                                                <>
                                                    <i className="bi bi-clipboard me-1"></i>{' '}
                                                    Copy
                                                </>
                                            )}
                                        </button>
                                    </div>
                                    <a
                                        href="#endpoints"
                                        className="btn btn-sm btn-outline-secondary rounded-pill px-3"
                                    >
                                        <i className="bi bi-list-ul me-1"></i>{' '}
                                        Browse Endpoints
                                    </a>
                                </div>
                            </div>

                            <div className="col-lg-5">
                                <div className="card border-0 shadow-sm bg-dark text-light rounded-4 overflow-hidden">
                                    <div className="card-header bg-dark border-secondary border-opacity-25 d-flex align-items-center justify-content-between py-2 px-3">
                                        <div className="d-flex align-items-center gap-2">
                                            <span
                                                className="rounded-circle bg-danger d-inline-block"
                                                style={{
                                                    width: 10,
                                                    height: 10,
                                                }}
                                            ></span>
                                            <span
                                                className="rounded-circle bg-warning d-inline-block"
                                                style={{
                                                    width: 10,
                                                    height: 10,
                                                }}
                                            ></span>
                                            <span
                                                className="rounded-circle bg-success d-inline-block"
                                                style={{
                                                    width: 10,
                                                    height: 10,
                                                }}
                                            ></span>
                                            <span className="ms-2 small text-secondary font-monospace">
                                                quick-test.sh
                                            </span>
                                        </div>
                                        <button
                                            type="button"
                                            className="btn btn-sm text-secondary p-0"
                                            onClick={() =>
                                                handleCopy(
                                                    `curl -X GET "${getOrigin()}/api/profiles" \\\n  -H "Accept: application/json"`,
                                                    'curl-preview',
                                                )
                                            }
                                            title="Copy snippet"
                                        >
                                            {copiedSnippet ===
                                            'curl-preview' ? (
                                                <span className="text-success small">
                                                    <i className="bi bi-check-lg"></i>{' '}
                                                    Copied
                                                </span>
                                            ) : (
                                                <i className="bi bi-clipboard small"></i>
                                            )}
                                        </button>
                                    </div>
                                    <div className="card-body p-3 font-monospace small">
                                        <div className="text-secondary mb-1">
                                            # 1. Fetch public profile
                                        </div>
                                        <div className="text-success">
                                            curl -X GET &quot;{getOrigin()}
                                            /api/profiles&quot; \
                                        </div>
                                        <div className="text-success ps-3">
                                            -H &quot;Accept:
                                            application/json&quot;
                                        </div>

                                        <div className="text-secondary mb-1">
                                            # 2. Fetch public portfolio
                                        </div>
                                        <div className="text-info">
                                            curl -X GET &quot;{getOrigin()}
                                            /api/portfolios&quot; \
                                        </div>
                                        <div className="text-info ps-3">
                                            -H &quot;Accept:
                                            application/json&quot;
                                        </div>

                                        {/* <div className="text-secondary mt-3 mb-1">
                                            # 2. Authenticate admin user
                                        </div>
                                        <div className="text-info">
                                            curl -X POST &quot;{getOrigin()}
                                            /api/login&quot; \
                                        </div>
                                        <div className="text-info ps-3">
                                            -H &quot;Content-Type:
                                            application/json&quot; \
                                        </div>
                                        <div className="text-info ps-3">
                                            -d &apos;&#123;&quot;email&quot;:
                                            &quot;...&quot;,
                                            &quot;password&quot;:
                                            &quot;...&quot;&#125;&apos;
                                        </div> */}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                {/* Core Architecture Highlights */}
                <section className="py-4 border-bottom bg-white">
                    <div className="container">
                        <div className="row g-3">
                            <div className="col-md-6 col-xl-3">
                                <div className="card h-100 border-0 bg-light rounded-3 p-3">
                                    <div className="d-flex align-items-center gap-3 mb-2">
                                        <span className="p-2 bg-primary-subtle text-primary rounded-2">
                                            <i className="bi bi-shield-lock-fill fs-5"></i>
                                        </span>
                                        <div>
                                            <h6 className="mb-0 fw-bold">
                                                Sanctum Auth
                                            </h6>
                                            <small className="text-muted">
                                                Bearer Token Protection
                                            </small>
                                        </div>
                                    </div>
                                    <p className="small text-muted mb-0">
                                        Protected write/update operations
                                        requiring secure Sanctum tokens, with
                                        login throttle protection.
                                    </p>
                                </div>
                            </div>

                            <div className="col-md-6 col-xl-3">
                                <div className="card h-100 border-0 bg-light rounded-3 p-3">
                                    <div className="d-flex align-items-center gap-3 mb-2">
                                        <span className="p-2 bg-success-subtle text-success rounded-2">
                                            <i className="bi bi-diagram-3-fill fs-5"></i>
                                        </span>
                                        <div>
                                            <h6 className="mb-0 fw-bold">
                                                RESTful Standards
                                            </h6>
                                            <small className="text-muted">
                                                Eloquent Resources
                                            </small>
                                        </div>
                                    </div>
                                    <p className="small text-muted mb-0">
                                        Standardized JSON output structures with
                                        clean pagination and eager-loaded
                                        relationships.
                                    </p>
                                </div>
                            </div>

                            <div className="col-md-6 col-xl-3">
                                <div className="card h-100 border-0 bg-light rounded-3 p-3">
                                    <div className="d-flex align-items-center gap-3 mb-2">
                                        <span className="p-2 bg-warning-subtle text-warning-emphasis rounded-2">
                                            <i className="bi bi-arrow-counterclockwise fs-5"></i>
                                        </span>
                                        <div>
                                            <h6 className="mb-0 fw-bold">
                                                Soft-Delete Restore
                                            </h6>
                                            <small className="text-muted">
                                                Safe Data Recovery
                                            </small>
                                        </div>
                                    </div>
                                    <p className="small text-muted mb-0">
                                        Support for dedicated{' '}
                                        <code>/restore</code> endpoints across
                                        all major curriculum resources.
                                    </p>
                                </div>
                            </div>

                            <div className="col-md-6 col-xl-3">
                                <div className="card h-100 border-0 bg-light rounded-3 p-3">
                                    <div className="d-flex align-items-center gap-3 mb-2">
                                        <span className="p-2 bg-info-subtle text-info-emphasis rounded-2">
                                            <i className="bi bi-heart-pulse-fill fs-5"></i>
                                        </span>
                                        <div>
                                            <h6 className="mb-0 fw-bold">
                                                Health Probing
                                            </h6>
                                            <small className="text-muted">
                                                High Availability
                                            </small>
                                        </div>
                                    </div>
                                    <p className="small text-muted mb-0">
                                        Zero-downtime deploy friendly with
                                        integrated Laravel <code>/up</code>{' '}
                                        health check endpoints.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {/* API Directory */}
                <main className="container py-5 flex-grow-1" id="endpoints">
                    <div className="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
                        <div>
                            <h2 className="h4 fw-bold text-dark mb-1">
                                Available API Endpoints
                            </h2>
                            <p className="text-muted small mb-0">
                                Showing {filteredEndpoints.length} of{' '}
                                {ENDPOINTS.length} available resources
                            </p>
                        </div>

                        {/* Search & Category Filter */}
                        <div className="d-flex flex-wrap align-items-center gap-2 w-100 w-md-auto">
                            <div
                                className="input-group input-group-sm"
                                style={{ maxWidth: 280 }}
                            >
                                <span className="input-group-text bg-white border-end-0">
                                    <i className="bi bi-search text-muted"></i>
                                </span>
                                <input
                                    type="text"
                                    className="form-control border-start-0"
                                    placeholder="Filter by name or path..."
                                    value={searchQuery}
                                    onChange={(e) =>
                                        setSearchQuery(e.target.value)
                                    }
                                />
                                {searchQuery && (
                                    <button
                                        type="button"
                                        className="btn btn-outline-secondary border-start-0"
                                        onClick={() => setSearchQuery('')}
                                    >
                                        <i className="bi bi-x"></i>
                                    </button>
                                )}
                            </div>
                        </div>
                    </div>

                    {/* Category Filter Pills */}
                    <div className="d-flex flex-wrap gap-2 mb-4">
                        {categories.map((cat) => (
                            <button
                                key={cat}
                                type="button"
                                className={`btn btn-sm rounded-pill px-3 ${
                                    selectedCategory === cat
                                        ? 'btn-dark'
                                        : 'btn-outline-secondary border-0 bg-white shadow-sm'
                                }`}
                                onClick={() => setSelectedCategory(cat)}
                            >
                                {cat}
                            </button>
                        ))}
                    </div>

                    {/* Endpoint List */}
                    <div className="card border-0 shadow-sm rounded-3 overflow-hidden">
                        <div className="table-responsive">
                            <table className="table table-hover align-middle mb-0">
                                <thead className="table-light border-bottom text-uppercase text-muted fs-7">
                                    <tr>
                                        <th
                                            scope="col"
                                            style={{ width: '22%' }}
                                            className="ps-4"
                                        >
                                            Resource
                                        </th>
                                        <th
                                            scope="col"
                                            style={{ width: '28%' }}
                                        >
                                            Endpoint
                                        </th>
                                        <th
                                            scope="col"
                                            style={{ width: '15%' }}
                                        >
                                            Methods
                                        </th>
                                        <th
                                            scope="col"
                                            style={{ width: '25%' }}
                                        >
                                            Description
                                        </th>
                                        <th
                                            scope="col"
                                            style={{ width: '10%' }}
                                            className="text-end pe-4"
                                        >
                                            Action
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {filteredEndpoints.length === 0 ? (
                                        <tr>
                                            <td
                                                colSpan={5}
                                                className="text-center py-5 text-muted"
                                            >
                                                <i className="bi bi-search fs-2 d-block mb-2"></i>
                                                No endpoints matching &quot;
                                                {searchQuery}&quot; in category
                                                &quot;{selectedCategory}&quot;
                                            </td>
                                        </tr>
                                    ) : (
                                        filteredEndpoints.map((ep) => (
                                            <tr key={ep.path}>
                                                <td className="ps-4">
                                                    <div className="fw-semibold text-dark">
                                                        {ep.name}
                                                    </div>
                                                    <span className="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill fs-8">
                                                        {ep.category}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div className="d-flex align-items-center gap-2">
                                                        <code className="text-primary fw-medium font-monospace bg-primary-subtle px-2 py-1 rounded">
                                                            {ep.path}
                                                        </code>
                                                        {ep.hasRestore && (
                                                            <span
                                                                className="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill fs-8"
                                                                title="Supports POST .../{id}/restore"
                                                            >
                                                                +restore
                                                            </span>
                                                        )}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div className="d-flex flex-wrap gap-1">
                                                        {ep.methods.map(
                                                            (method) => {
                                                                let badgeClass =
                                                                    'bg-secondary';
                                                                if (
                                                                    method ===
                                                                    'GET'
                                                                )
                                                                    badgeClass =
                                                                        'bg-success';
                                                                if (
                                                                    method ===
                                                                    'POST'
                                                                )
                                                                    badgeClass =
                                                                        'bg-primary';
                                                                if (
                                                                    method ===
                                                                    'PUT'
                                                                )
                                                                    badgeClass =
                                                                        'bg-warning text-dark';
                                                                if (
                                                                    method ===
                                                                    'DELETE'
                                                                )
                                                                    badgeClass =
                                                                        'bg-danger';

                                                                return (
                                                                    <span
                                                                        key={
                                                                            method
                                                                        }
                                                                        className={`badge ${badgeClass} font-monospace fs-8`}
                                                                    >
                                                                        {method}
                                                                    </span>
                                                                );
                                                            },
                                                        )}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div className="small text-muted">
                                                        {ep.description}
                                                    </div>
                                                    <div className="mt-1 d-flex flex-wrap gap-1">
                                                        {ep.access ===
                                                            'public' && (
                                                            <span className="badge bg-success-subtle text-success border border-success-subtle rounded-pill fs-8">
                                                                <i className="bi bi-globe me-1"></i>
                                                                Public
                                                            </span>
                                                        )}
                                                        {ep.access ===
                                                            'auth' && (
                                                            <span className="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill fs-8">
                                                                <i className="bi bi-lock-fill me-1"></i>
                                                                Bearer Token
                                                            </span>
                                                        )}
                                                        {ep.access ===
                                                            'public-read' && (
                                                            <>
                                                                <span className="badge bg-success-subtle text-success border border-success-subtle rounded-pill fs-8">
                                                                    <i className="bi bi-globe me-1"></i>
                                                                    Public Read
                                                                </span>
                                                                <span className="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill fs-8">
                                                                    <i className="bi bi-lock-fill me-1"></i>
                                                                    Auth for
                                                                    Mutations
                                                                </span>
                                                            </>
                                                        )}
                                                    </div>
                                                </td>
                                                <td className="text-end pe-4">
                                                    <div className="btn-group btn-group-sm">
                                                        <a
                                                            href={ep.path}
                                                            target="_blank"
                                                            rel="noreferrer"
                                                            className="btn btn-outline-secondary"
                                                            title="Open endpoint in new tab"
                                                        >
                                                            <i className="bi bi-box-arrow-up-right"></i>
                                                        </a>
                                                        <button
                                                            type="button"
                                                            className="btn btn-outline-secondary"
                                                            title="Copy path"
                                                            onClick={() =>
                                                                handleCopy(
                                                                    `${getOrigin()}${ep.path}`,
                                                                    ep.path,
                                                                )
                                                            }
                                                        >
                                                            {copiedSnippet ===
                                                            ep.path ? (
                                                                <i className="bi bi-check text-success"></i>
                                                            ) : (
                                                                <i className="bi bi-clipboard"></i>
                                                            )}
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        ))
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </main>

                {/* Footer */}
                <Footer
                    appName={appName}
                    laravelVersion={laravelVersion}
                    phpVersion={phpVersion}
                />
            </div>
        </>
    );
}
