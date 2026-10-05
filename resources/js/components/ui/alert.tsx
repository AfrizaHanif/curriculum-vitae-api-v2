"use client";

import React from "react";

export interface AlertProps {
  color?:
    | "primary"
    | "secondary"
    | "success"
    | "danger"
    | "warning"
    | "info"
    | "light"
    | "dark"
    | (string & {});
  icon?: string | React.ReactNode;
  dismissable?: boolean;
  children: React.ReactNode;
  className?: string;
  style?: React.CSSProperties;
  onClose?: () => void;
}

export default function Alert({
  color,
  dismissable = false,
  icon,
  children,
  className = "",
  style,
  onClose,
}: AlertProps) {
  const hasCustomAlert = /\balert-/.test(className);
  const resolvedColor = color ?? (hasCustomAlert ? undefined : "primary");
  const colorClass = resolvedColor ? `alert-${resolvedColor}` : "";

  const alertClasses = `alert ${colorClass} ${dismissable ? "alert-dismissible" : ""} fade show ${className}`
    .trim()
    .replace(/\s+/g, " ");

  const renderIcon = () => {
    if (!icon) return null;
    if (typeof icon === "string") {
      if (icon.startsWith("bi-")) {
        return <i className={`bi ${icon} flex-shrink-0 me-2`} />;
      }
      return (
        <svg className="bi flex-shrink-0 me-2" role="img" aria-label={icon}>
          <use xlinkHref={icon} />
        </svg>
      );
    }
    return <span className="flex-shrink-0 me-2 d-inline-flex">{icon}</span>;
  };

  return (
    <div className={alertClasses} style={style} role="alert">
      {renderIcon()}
      {children}
      {dismissable && (
        <button
          type="button"
          className="btn-close"
          data-bs-dismiss={onClose ? undefined : "alert"}
          onClick={onClose}
          aria-label="Close"
        />
      )}
    </div>
  );
}
