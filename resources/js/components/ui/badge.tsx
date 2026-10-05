"use client";

import React from "react";

export interface BadgeProps extends React.HTMLAttributes<HTMLSpanElement> {
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
  subtle?: boolean;
  pill?: boolean;
  rounded?: boolean;
  className?: string;
  style?: React.CSSProperties;
  children: React.ReactNode;
}

export default function Badge({
  color,
  subtle = false,
  pill,
  rounded,
  className = "",
  style,
  children,
  ...rest
}: BadgeProps) {
  // If color is specified, use bg-{color}. If omitted, only default to bg-primary if no custom bg-* exists in className.
  const hasCustomBg = /\bbg-/.test(className);
  const resolvedColor = color ?? (hasCustomBg ? undefined : "primary");

  let colorClasses = "";
  if (resolvedColor) {
    if (subtle) {
      colorClasses = `bg-${resolvedColor}-subtle text-${resolvedColor}-emphasis border border-${resolvedColor}-subtle`;
    } else {
      colorClasses = `bg-${resolvedColor}`;
    }
  }

  const shapeClass = pill ? "rounded-pill" : rounded ? "rounded" : "";

  const badgeClasses = `badge ${colorClasses} ${shapeClass} ${className}`
    .trim()
    .replace(/\s+/g, " ");

  return (
    <span className={badgeClasses} style={style} {...rest}>
      {children}
    </span>
  );
}
