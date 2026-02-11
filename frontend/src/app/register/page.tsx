'use client';

import { useState, FormEvent, useEffect } from 'react';
import { useAuth } from '@/lib/auth';
import { useRouter } from 'next/navigation';
import Image from 'next/image';
import Link from 'next/link';

export default function RegisterPage() {
  const { register, user, loading } = useAuth();
  const router = useRouter();
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [passwordConfirmation, setPasswordConfirmation] = useState('');
  const [error, setError] = useState('');
  const [submitting, setSubmitting] = useState(false);

  useEffect(() => {
    if (!loading && user) {
      router.replace('/projects');
    }
  }, [user, loading, router]);

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault();
    setError('');
    if (password !== passwordConfirmation) {
      setError('Passwords do not match');
      return;
    }
    setSubmitting(true);
    try {
      await register(name, email, password, passwordConfirmation);
      router.push('/projects');
    } catch (err: unknown) {
      setError(err instanceof Error ? err.message : 'Registration failed');
    } finally {
      setSubmitting(false);
    }
  };

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-yellow-400" />
      </div>
    );
  }

  return (
    <div className="min-h-screen flex flex-col items-center justify-center bg-gray-50 px-4">
      <div className="flex flex-col items-center mb-8">
        <Image src="/ninja-logo.svg" alt="TO-DO.NINJA" width={64} height={64} />
        <h1
          className="text-2xl tracking-wider text-gray-700 mt-4"
          style={{ fontFamily: "'Julius Sans One', sans-serif" }}
        >
          TO-DO.NINJA
        </h1>
      </div>

      <div className="w-full max-w-sm">
        <h2 className="text-xl text-center text-gray-600 mb-6" style={{ fontFamily: "'Julius Sans One', sans-serif" }}>
          Create Account
        </h2>

        {error && (
          <div className="bg-red-50 text-red-600 text-sm p-3 rounded-lg mb-4">
            {error}
          </div>
        )}

        <form onSubmit={handleSubmit} className="space-y-4">
          <input
            type="text"
            value={name}
            onChange={(e) => setName(e.target.value)}
            placeholder="Full Name"
            required
            className="w-full px-4 py-3 rounded-full border border-gray-300 text-sm focus:outline-none focus:border-yellow-400 focus:ring-1 focus:ring-yellow-400"
          />
          <input
            type="email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            placeholder="Email"
            required
            className="w-full px-4 py-3 rounded-full border border-gray-300 text-sm focus:outline-none focus:border-yellow-400 focus:ring-1 focus:ring-yellow-400"
          />
          <input
            type="password"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            placeholder="Password"
            required
            minLength={8}
            className="w-full px-4 py-3 rounded-full border border-gray-300 text-sm focus:outline-none focus:border-yellow-400 focus:ring-1 focus:ring-yellow-400"
          />
          <input
            type="password"
            value={passwordConfirmation}
            onChange={(e) => setPasswordConfirmation(e.target.value)}
            placeholder="Confirm Password"
            required
            minLength={8}
            className="w-full px-4 py-3 rounded-full border border-gray-300 text-sm focus:outline-none focus:border-yellow-400 focus:ring-1 focus:ring-yellow-400"
          />
          <button
            type="submit"
            disabled={submitting}
            className="w-full py-3 rounded-full text-white font-semibold text-sm bg-gradient-to-r from-[#FFE000] to-[#FFC200] hover:opacity-90 transition-opacity disabled:opacity-50 cursor-pointer"
          >
            {submitting ? 'Creating account...' : 'Sign Up'}
          </button>
        </form>

        <div className="mt-4 text-center text-sm text-gray-500">
          Already have an account?{' '}
          <Link href="/login" className="text-yellow-500 hover:text-yellow-600 font-medium">
            Sign In
          </Link>
        </div>
      </div>
    </div>
  );
}
