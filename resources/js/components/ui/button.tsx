"use client";

import React, { forwardRef } from "react";
import Tooltip from "./tooltip";
import Popover from "./popover";
import { Link } from "@inertiajs/react";

export interface ButtonProps extends React.AriaAttributes {
    id?: string;
    tabIndex?: number;
    title?: string;
    as?: "button" | "a";
    color?:
        | "primary"
        | "secondary"
        | "success"
        | "danger"
        | "warning"
        | "info"
        | "light"
        | "dark"
        | "link"
        | "outline-primary"
        | "outline-secondary"
        | "outline-success"
        | "outline-danger"
        | "outline-warning"
        | "outline-info"
        | "outline-light"
        | "outline-dark"
        | (string & {});
    size?: "sm" | "lg";
    type?: "button" | "submit" | "reset";
    disabled?: boolean;
    href?: string;
    scroll?: boolean;
    preserveScroll?: boolean;
    target?: string;
    rel?: string;
    fullWidth?: boolean;
    rounded?: boolean;
    dataBsToggle?: "tooltip" | "modal" | "popover" | "offcanvas" | "dropdown";
    dataBsTarget?: string;
    dataBsSubject?: string;
    dataBsPlacement?: "top" | "bottom" | "left" | "right";
    dataBsTitle?: string;
    dataBsContent?: string | React.ReactNode;
    dataBsTrigger?: "hover" | "focus" | "click";
    dataBsDismiss?: "modal" | "offcanvas";
    dataBsCustomClass?: string;
    onClick?: (
        event: React.MouseEvent<HTMLButtonElement | HTMLAnchorElement>,
    ) => void;
    style?: React.CSSProperties;
    className?: string;
    children?: React.ReactNode;
}

const Button = forwardRef<HTMLButtonElement | HTMLAnchorElement, ButtonProps>(
    function Button(
        {
            as = "button",
            color,
            size,
            type = "button",
            disabled = false,
            href,
            scroll,
            preserveScroll,
            target,
            rel,
            fullWidth = false,
            rounded = false,
            dataBsToggle,
            dataBsTarget,
            dataBsSubject,
            dataBsTitle,
            dataBsPlacement,
            dataBsContent,
            dataBsTrigger,
            dataBsDismiss,
            dataBsCustomClass,
            onClick,
            style,
            className = "",
            children,
            id,
            tabIndex,
            title,
            ...ariaProps
        },
        ref,
    ) {
        const hasCustomBtn = /\bbtn-/.test(className);
        const resolvedColor = color ?? (hasCustomBtn ? undefined : "primary");
        const colorClass = resolvedColor ? `btn-${resolvedColor}` : "";
        const sizeClass = size ? `btn-${size}` : "";
        const roundedClass = rounded ? "rounded-pill" : "";
        const fullWidthClass = fullWidth ? "w-100" : "";
        const disabledClass = disabled ? "disabled" : "";

        const combinedClasses =
            `btn ${colorClass} ${sizeClass} ${roundedClass} ${fullWidthClass} ${disabledClass} ${className}`
                .trim()
                .replace(/\s+/g, " ");

        const content =
            as === "a" || href ? (
                <Link
                    ref={ref as React.Ref<HTMLAnchorElement>}
                    id={id}
                    tabIndex={tabIndex}
                    title={title}
                    href={href || "#"}
                    preserveScroll={preserveScroll ?? scroll}
                    target={target}
                    rel={rel}
                    className={combinedClasses}
                    style={
                        disabled ? { ...style, pointerEvents: "none" } : style
                    }
                    data-bs-toggle={dataBsToggle}
                    data-bs-target={dataBsTarget}
                    data-bs-title={dataBsTitle}
                    data-bs-trigger={dataBsTrigger}
                    data-bs-dismiss={dataBsDismiss}
                    {...ariaProps}
                    onClick={(e) => {
                        if (disabled) {
                            e.preventDefault();
                            return;
                        }
                        if (dataBsToggle === "modal" && dataBsSubject) {
                            window.dispatchEvent(
                                new CustomEvent("set-lead-modal-subject", {
                                    detail: dataBsSubject,
                                }),
                            );
                        }
                        if (onClick) onClick(e as React.MouseEvent<HTMLAnchorElement>);
                    }}
                >
                    {children}
                </Link>
            ) : (
                <button
                    ref={ref as React.Ref<HTMLButtonElement>}
                    id={id}
                    tabIndex={tabIndex}
                    title={title}
                    className={combinedClasses}
                    style={
                        disabled ? { ...style, pointerEvents: "none" } : style
                    }
                    type={type}
                    disabled={disabled}
                    data-bs-toggle={dataBsToggle}
                    data-bs-target={dataBsTarget}
                    data-bs-title={dataBsTitle}
                    data-bs-trigger={dataBsTrigger}
                    data-bs-dismiss={dataBsDismiss}
                    {...ariaProps}
                    onClick={(e) => {
                        if (dataBsToggle === "modal" && dataBsSubject) {
                            window.dispatchEvent(
                                new CustomEvent("set-lead-modal-subject", {
                                    detail: dataBsSubject,
                                }),
                            );
                        }
                        if (onClick) onClick(e);
                    }}
                >
                    {children}
                </button>
            );

        if (dataBsToggle === "tooltip") {
            if (disabled) {
                return (
                    <Tooltip
                        title={dataBsTitle as string}
                        placement={dataBsPlacement}
                        customClass={dataBsCustomClass}
                    >
                        <span
                            className="d-inline-block"
                            style={{
                                cursor: "not-allowed",
                                pointerEvents: "auto",
                            }}
                        >
                            {content}
                        </span>
                    </Tooltip>
                );
            }

            return (
                <Tooltip
                    title={dataBsTitle as string}
                    placement={dataBsPlacement}
                    customClass={dataBsCustomClass}
                >
                    {content}
                </Tooltip>
            );
        }

        if (dataBsToggle === "popover") {
            const popoverContent = dataBsContent || "";
            const popoverTrigger = dataBsTrigger || "click";

            if (disabled) {
                return (
                    <Popover
                        title={dataBsTitle}
                        content={popoverContent}
                        placement={dataBsPlacement}
                        trigger={popoverTrigger}
                    >
                        <span
                            className="d-inline-block"
                            style={{
                                cursor: "not-allowed",
                                pointerEvents: "auto",
                            }}
                        >
                            {content}
                        </span>
                    </Popover>
                );
            }

            return (
                <Popover
                    title={dataBsTitle}
                    content={popoverContent}
                    placement={dataBsPlacement}
                    trigger={popoverTrigger}
                >
                    {content}
                </Popover>
            );
        }

        return content;
    },
);

export default Button;
