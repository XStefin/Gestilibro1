/**
 * StatusBadge – badge de estado genérico.
 * variant: "success" | "danger" | "warning" | "secondary"
 */
export default function StatusBadge({ label, variant = "secondary" }) {
  return <span className={`badge badge-${variant}`}>{label}</span>;
}
