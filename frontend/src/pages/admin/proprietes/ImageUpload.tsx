/* cspell:disable */

import { useState, useRef } from "react";
import { useMutation, useQueryClient } from "@tanstack/react-query";
import { Button } from "@/components/ui/button";
import { toast } from "sonner";
import apiClient from "@/shared/api/client";

interface Props {
  proprieteId: number;
}

export function ImageUpload({ proprieteId }: Readonly<Props>) {
  const queryClient = useQueryClient();
  const inputRef = useRef<HTMLInputElement>(null);
  const [previews, setPreviews] = useState<string[]>([]);

  const { mutate, isPending } = useMutation({
    mutationFn: (files: File[]) => {
      const form = new FormData();
      files.forEach((f) => form.append("images[]", f));
      return apiClient.post(`/admin/proprietes/${proprieteId}/images`, form, {
        headers: { "Content-Type": "multipart/form-data" },
      });
    },
    onSuccess: (res) => {
      toast.success(res.data.message);
      setPreviews([]);
      queryClient.invalidateQueries({ queryKey: ["admin-proprietes"] });
      queryClient.invalidateQueries({ queryKey: ["propriete", proprieteId] });
    },
    onError: (err: any) =>
      toast.error(err?.response?.data?.message ?? "Erreur lors de l'upload."),
  });

  function handleChange(e: React.ChangeEvent<HTMLInputElement>) {
    const files = Array.from(e.target.files ?? []);
    if (files.length === 0) return;
    setPreviews(files.map((f) => URL.createObjectURL(f)));
  }

  function handleUpload() {
    const files = Array.from(inputRef.current?.files ?? []);
    if (files.length === 0) {
      toast.error("Sélectionnez au moins une image.");
      return;
    }
    mutate(files);
  }

  return (
    <div className="space-y-3">
      <input
        ref={inputRef}
        type="file"
        accept="image/*"
        multiple
        className="block text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-sm file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200"
        onChange={handleChange}
      />

      {previews.length > 0 && (
        <div className="flex flex-wrap gap-2">
          {previews.map((src, i) => (
            <img
              key={i}
              src={src}
              className="h-20 w-20 object-cover rounded border"
              alt=""
            />
          ))}
        </div>
      )}

      <Button
        size="sm"
        onClick={handleUpload}
        disabled={isPending || previews.length === 0}
      >
        {isPending
          ? "Upload en cours..."
          : `Uploader ${previews.length} image(s)`}
      </Button>
    </div>
  );
}
