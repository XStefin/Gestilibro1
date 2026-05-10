import StatusBadge from "../ui/StatusBadge";

/**
 * AvailabilityBadge – badge de disponibilidad de un libro.
 */
export default function AvailabilityBadge({ disponibilidad }) {
  if (disponibilidad === "disponible") {
    return <StatusBadge label="Disponible" variant="success" />;
  }
  return <StatusBadge label="No disponible" variant="secondary" />;
}
