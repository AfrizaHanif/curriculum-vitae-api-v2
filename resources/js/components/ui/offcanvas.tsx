import React, { useEffect, useRef, useSyncExternalStore } from "react";
import { createPortal } from "react-dom";
import type { Offcanvas as BootstrapOffcanvas } from "bootstrap";

export interface OffcanvasProps {
  id: string;
  /** Controls the visibility of the offcanvas */
  show?: boolean;
  /** Callback triggered when the offcanvas is closed */
  onClose?: () => void;
  /** Offcanvas header title */
  title?: React.ReactNode;
  /** Placement of the offcanvas drawer */
  placement?: "start" | "end" | "top" | "bottom";
  /** Offcanvas body content */
  children: React.ReactNode;
  /** Enable background scrolling while open */
  scroll?: boolean;
  /** Enable static backdrop or standard backdrop */
  backdrop?: boolean | "static";
  /** Show close button in the header */
  showCloseButton?: boolean;
  /** Additional custom class names for the outer offcanvas container */
  className?: string;
  /** Additional custom style for the outer offcanvas container */
  style?: React.CSSProperties;
  /** Additional custom class names for the header */
  headerClassName?: string;
  /** Additional custom class names for the body */
  bodyClassName?: string;
  /** Automatically reset the offcanvas and offcanvas body scroll position when closed */
  resetScrollOnClose?: boolean;
}

const subscribe = () => () => {};
const getSnapshot = () => true;
const getServerSnapshot = () => false;

export default function Offcanvas({
  id,
  show = false,
  onClose,
  title,
  placement = "end",
  children,
  scroll = false,
  backdrop = true,
  showCloseButton = true,
  className = "",
  style,
  headerClassName = "",
  bodyClassName = "",
  resetScrollOnClose = true,
}: OffcanvasProps) {
  const mounted = useSyncExternalStore(
    subscribe,
    getSnapshot,
    getServerSnapshot
  );

  const offcanvasRef = useRef<HTMLDivElement | null>(null);
  const offcanvasInstanceRef = useRef<BootstrapOffcanvas | null>(null);

  // Synchronize offcanvas state with Bootstrap JS API
  useEffect(() => {
    const node = offcanvasRef.current;
    if (!node) return;

    if (show) {
      // Dynamically import Bootstrap to avoid SSR errors in Next.js
      import("bootstrap").then((bootstrap) => {
        if (!document.body.contains(node)) return;

        const instance =
          bootstrap.Offcanvas.getInstance(node) ||
          new bootstrap.Offcanvas(node, {
            backdrop: backdrop,
            scroll: scroll,
            keyboard: backdrop !== "static",
          });
        offcanvasInstanceRef.current = instance;
        instance.show();
      });
    } else {
      // Hide the offcanvas if the prop changes to false
      if (offcanvasInstanceRef.current) {
        offcanvasInstanceRef.current.hide();
      }
    }
  }, [show, backdrop, scroll, mounted]);

  // Handle bootstrap close events (backdrop click, escape key, close buttons)
  useEffect(() => {
    const node = offcanvasRef.current;
    if (!node) return;

    const handleHidden = () => {
      if (resetScrollOnClose) {
        node.scrollTop = 0;
        const body = node.querySelector<HTMLElement>(".offcanvas-body");
        if (body) {
          body.scrollTop = 0;
        }
      }

      // Preserve modal scroll lock if a modal is still open underneath
      if (document.querySelector(".modal.show")) {
        document.body.classList.add("modal-open");
        document.body.style.overflow = "hidden";
      }

      if (onClose) {
        onClose();
      }
    };

    node.addEventListener("hidden.bs.offcanvas", handleHidden);
    return () => {
      node.removeEventListener("hidden.bs.offcanvas", handleHidden);
    };
  }, [onClose, mounted, resetScrollOnClose]);

  // Clean up and dispose of the offcanvas instance on component unmount
  useEffect(() => {
    return () => {
      const instance = offcanvasInstanceRef.current;
      if (instance) {
        // Delay dispose to allow any active transitions to finish gracefully
        setTimeout(() => {
          try {
            instance.dispose();
          } catch (e) {
            console.error("Error disposing offcanvas instance:", e);
          }
        }, 500);
        offcanvasInstanceRef.current = null;
      }

      // Safeguard: manually remove Bootstrap offcanvas backdrop and classes/styles from body
      // to prevent frozen page issues during React component unmount/navigation.
      document.body.classList.remove("offcanvas-open");
      document.body.style.removeProperty("overflow");
      document.body.style.removeProperty("padding-right");
      const backdrops = document.querySelectorAll(".offcanvas-backdrop");
      backdrops.forEach((backdrop) => backdrop.remove());
    };
  }, []);

  const offcanvasElement = (
    <div
      id={id}
      ref={offcanvasRef}
      className={`offcanvas offcanvas-${placement} ${className}`}
      style={style}
      tabIndex={-1}
      aria-labelledby={`${id}-label`}
      data-bs-scroll={scroll}
      data-bs-backdrop={backdrop === "static" ? "static" : backdrop}
    >
      {(title || showCloseButton) && (
        <div className={`offcanvas-header ${headerClassName}`}>
          <h5
            className="offcanvas-title flex-grow-1 me-3 text-truncate"
            style={{ minWidth: 0 }}
            id={`${id}-label`}
          >
            {title}
          </h5>
          {showCloseButton && (
            <button
              type="button"
              className="btn-close text-reset flex-shrink-0"
              data-bs-dismiss="offcanvas"
              aria-label="Close"
              onClick={onClose}
            />
          )}
        </div>
      )}
      <div className={`offcanvas-body ${bodyClassName}`}>{children}</div>
    </div>
  );

  if (!mounted || typeof document === "undefined") return null;

  return createPortal(offcanvasElement, document.body);
}
