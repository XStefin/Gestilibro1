import StatusBadge from "../ui/StatusBadge";

const ESTADO_VARIANT = {
  prestado: "warning",
  devuelto: "success",
  atrasado: "danger",
};

/**
 * LoanStatusBadge – badge de estado de préstamo.
 */
export default function LoanStatusBadge({ estado }) {
  const normalized = String(estado || "").toLowerCase();
  const variant = ESTADO_VARIANT[normalized] ?? "secondary";
  const label = estado
    ? estado.charAt(0).toUpperCase() + estado.slice(1).toLowerCase()
    : "-";
  return <StatusBadge label={label} variant={variant} />;
}
