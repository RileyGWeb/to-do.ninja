'use client';

import { useState, useRef, useEffect, KeyboardEvent } from 'react';

interface InlineEditProps {
  value: string;
  onSave: (value: string) => void;
  className?: string;
  inputClassName?: string;
  tag?: 'h1' | 'h2' | 'h3' | 'span' | 'div';
}

export default function InlineEdit({ value, onSave, className = '', inputClassName = '', tag: Tag = 'span' }: InlineEditProps) {
  const [editing, setEditing] = useState(false);
  const [editValue, setEditValue] = useState(value);
  const inputRef = useRef<HTMLInputElement>(null);

  useEffect(() => {
    setEditValue(value);
  }, [value]);

  useEffect(() => {
    if (editing && inputRef.current) {
      inputRef.current.focus();
      inputRef.current.select();
    }
  }, [editing]);

  const handleSave = () => {
    const trimmed = editValue.trim();
    if (trimmed && trimmed !== value) {
      onSave(trimmed);
    } else {
      setEditValue(value);
    }
    setEditing(false);
  };

  const handleKeyDown = (e: KeyboardEvent<HTMLInputElement>) => {
    if (e.key === 'Enter') {
      handleSave();
    } else if (e.key === 'Escape') {
      setEditValue(value);
      setEditing(false);
    }
  };

  if (editing) {
    return (
      <input
        ref={inputRef}
        type="text"
        value={editValue}
        onChange={(e) => setEditValue(e.target.value)}
        onBlur={handleSave}
        onKeyDown={handleKeyDown}
        className={`bg-transparent border-none outline-none focus:ring-0 p-0 ${inputClassName || className}`}
      />
    );
  }

  return (
    <Tag
      onClick={() => setEditing(true)}
      className={`cursor-pointer hover:opacity-70 transition-opacity ${className}`}
    >
      {value}
    </Tag>
  );
}
