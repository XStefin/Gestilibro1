/**
 * FormCard – contenedor card para formularios Create/Edit.
 * Props:
 *   icon    : ReactNode
 *   title   : string
 *   children: ReactNode
 */
export default function FormCard({ icon, title, children }) {
  return (
    <div className="form-card">
      <h2 className="form-card-title">
        {icon}
        <span>{title}</span>
      </h2>
      {children}
    </div>
  );
}
