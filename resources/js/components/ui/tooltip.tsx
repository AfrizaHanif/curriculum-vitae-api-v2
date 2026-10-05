"use client";

import React, { useEffect, useRef } from "react";
import type { Tooltip as BootstrapTooltip } from "bootstrap";

interface TooltipProps {
  children: React.ReactElement;
  title: string;
  placement?: "top" | "bottom" | "left" | "right";
  customClass?: string;
}

export default function Tooltip({
  children,
  title,
  placement = "top",
  customClass = "",
}: TooltipProps) {
  const triggerRef = useRef<HTMLElement | null>(null);
  const tooltipInstanceRef = useRef<BootstrapTooltip | null>(null);

  const child = React.Children.only(children) as React.ReactElement<{
    disabled?: boolean;
    className?: string;
    style?: React.CSSProperties;
    ref?: React.Ref<HTMLElement>;
  }>;

  const isDisabled =
    child.props.disabled ||
    (typeof child.props.className === "string" &&
      child.props.className.split(" ").includes("disabled"));

  const hideTooltip = () => {
    const instance = tooltipInstanceRef.current;
    if (instance) {
      const rawInstance = instance as unknown as {
        _isHovered?: boolean | null;
        _activeTrigger?: Record<string, boolean>;
        _timeout?: number;
      };
      rawInstance._isHovered = false;
      if (rawInstance._timeout) {
        clearTimeout(rawInstance._timeout);
      }
      if (rawInstance._activeTrigger) {
        rawInstance._activeTrigger.hover = false;
        rawInstance._activeTrigger.focus = false;
        rawInstance._activeTrigger.click = false;
      }
      try {
        instance.hide();
      } catch {
        // Ignore lifecycle errors
      }
    }
  };

  useEffect(() => {
    const node = triggerRef.current;
    if (!node) return;

    let active = true;
    let tooltipInstance: BootstrapTooltip | null = null;

    const handleClick = (e: MouseEvent) => {
      hideTooltip();

      // For mouse/pointer clicks (detail > 0), blur active button/link so it does not retain focus
      // while preserving keyboard navigation when triggered via Enter/Space (detail === 0)
      if (
        e.detail > 0 &&
        document.activeElement instanceof HTMLElement &&
        (document.activeElement.tagName === "BUTTON" ||
          document.activeElement.tagName === "A")
      ) {
        document.activeElement.blur();
      }
    };

    node.addEventListener("click", handleClick);

    // Dynamically import Bootstrap to prevent SSR errors
    import("bootstrap").then((bootstrap) => {
      if (!active) return;
      // Check if node is still connected/rendered
      if (!document.body.contains(node)) return;

      try {
        // Dispose of any existing instance on this node to prevent duplicates
        const existing = bootstrap.Tooltip.getInstance(node);
        if (existing) {
          existing.dispose();
        }

        tooltipInstance = new bootstrap.Tooltip(node, {
          title,
          placement,
          trigger: "hover focus",
          customClass,
        });
        tooltipInstanceRef.current = tooltipInstance;
      } catch {
        // Ignore lifecycle errors
      }
    });

    return () => {
      active = false;
      node.removeEventListener("click", handleClick);
      if (tooltipInstance) {
        try {
          tooltipInstance.dispose();
        } catch {
          // Ignore lifecycle errors
        }
      }
      if (tooltipInstanceRef.current === tooltipInstance) {
        tooltipInstanceRef.current = null;
      }
    };
  }, [title, placement, isDisabled, customClass]);

  const wrappedChild = React.cloneElement(child, {
    style: {
      ...child.props.style,
      // ...(isDisabled ? { pointerEvents: "none" } : {}),
    },
  });

  return (
    <span
      ref={triggerRef as React.Ref<HTMLSpanElement>}
      className="d-inline-block"
      onClick={hideTooltip}
      // style={{ cursor: isDisabled ? "not-allowed" : "default" }}
    >
      {wrappedChild}
    </span>
  );
}
