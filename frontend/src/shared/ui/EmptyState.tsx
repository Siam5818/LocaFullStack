/* cspell:disable */
interface EmptyStateProps {
  message: string;
  icon?: string;
}

export function EmptyState({ message, icon = '📭' }: Readonly<EmptyStateProps>) {
  return (
    <div className="flex flex-col items-center justify-center py-16 text-gray-400 text-sm gap-2">
      <span className="text-3xl">{icon}</span>
      <p>{message}</p>
    </div>
  );
}
