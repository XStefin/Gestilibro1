/**
 * PageHeader – encabezado de sección con título (+ icono) y acción opcional.
 * Props:
 *   icon    : ReactNode (ej. <FaBook />)
 *   title   : string
 *   action  : ReactNode (ej. un <Link> de "Nuevo")
 */
export default function PageHeader({ icon, title, action }) {
  return (
    <div className="page-header">
      <h4 className="page-title">
        {icon}
        <span>{title}</span>
      </h4>
      {action && <div className="page-header-action">{action}</div>}
    </div>
  );
}
