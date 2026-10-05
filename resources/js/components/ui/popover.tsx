"use client";

import React, { useRef, useId, useEffect } from "react";
import type { Popover as BootstrapPopover } from "bootstrap";

export interface PopoverProps {
  /** The single trigger element for the popover */
  children: React.ReactElement;
  /** Optional title for the popover header */
  title?: string;
  /** The content of the popover. Can be a plain string or a React node for rich HTML content */
  content: string | React.ReactNode;
  /** The placement of the popover relative to the trigger element */
  placement?: "top" | "bottom" | "left" | "right" | "auto";
  /** How the popover is triggered. Default is "click" */
  trigger?: "click" | "hover" | "focus";
  /** Whether HTML content is allowed. Automatically set to true if a React node is passed as content */
  html?: boolean;
  /** Custom CSS class name to apply to the popover element */
  customClass?: string;
}

export default function Popover({
  children,
  title = "",
  content,
  placement = "top",
  trigger = "click",
  html = false,
  customClass = "",
}: PopoverProps) {
  const triggerRef = useRef<HTMLElement | null>(null);
  const popoverInstanceRef = useRef<BootstrapPopover | null>(null);
  const contentContainerId = useId();

  useEffect(() => {
    const node = triggerRef.current;
    if (!node) return;

    let popoverInstance: BootstrapPopover | null = null;

    // Dynamically import Bootstrap to prevent SSR errors in Next.js
    import("bootstrap").then((bootstrap) => {
      // Check if node is still connected/rendered
      if (!document.body.contains(node)) return;

      // Dispose of any existing instance on this node to prevent duplicates
      const existing = bootstrap.Popover.getInstance(node);
      if (existing) {
        existing.dispose();
      }

      let popoverContent: string | Element = "";
      let useHtml = html;

      if (typeof content === "string") {
        popoverContent = content;
      } else if (content) {
        // If content is a React element, retrieve the hidden DOM element
        const element = document.getElementById(contentContainerId);
        if (element) {
          // Set display to block so it renders correctly inside the popover
          element.style.display = "block";
          popoverContent = element;
          useHtml = true;
        }
      }

      popoverInstance = new bootstrap.Popover(node, {
        title,
        content: popoverContent,
        placement,
        trigger,
        html: useHtml,
        customClass,
      });
      popoverInstanceRef.current = popoverInstance;
    });

    return () => {
      if (popoverInstance) {
        popoverInstance.dispose();
      }
      if (popoverInstanceRef.current === popoverInstance) {
        popoverInstanceRef.current = null;
      }
    };
  }, [title, content, placement, trigger, html, customClass, contentContainerId]);

  // Attach the ref to the single child element
  const child = React.Children.only(children) as React.ReactElement<{
    ref?: React.Ref<HTMLElement>;
  }>;

  return (
    <>
      {React.cloneElement(child, {
        ref: triggerRef,
      })}
      {typeof content !== "string" && content && (
        <div style={{ display: "none" }}>
          <div id={contentContainerId}>{content}</div>
        </div>
      )}
    </>
  );
}
