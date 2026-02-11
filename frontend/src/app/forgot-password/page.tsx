'use client';

import { useState, FormEvent } from 'react';
import Image from 'next/image';
import Link from 'next/link';

export default function ForgotPasswordPage() {
  const [email, setEmail] = useState('');
  const [submitted, setSubmitted] = useState(false);

  const handleSubmit = (e: FormEvent) => {
    e.preventDefault();
    setSubmitted(true);
  };

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
          Reset Password
        </h2>

        {submitted ? (
          <div className="text-center">
            <p className="text-sm text-gray-600 mb-4">
              If an account exists with that email, you will receive a password reset link shortly.
            </p>
            <Link href="/login" className="text-yellow-500 hover:text-yellow-600 font-medium text-sm">
              Back to Sign In
            </Link>
          </div>
        ) : (
          <>
            <form onSubmit={handleSubmit} className="space-y-4">
              <input
                type="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                placeholder="Email"
                required
                className="w-full px-4 py-3 rounded-full border border-gray-300 text-sm focus:outline-none focus:border-yellow-400 focus:ring-1 focus:ring-yellow-400"
              />
              <button
                type="submit"
                className="w-full py-3 rounded-full text-white font-semibold text-sm bg-gradient-to-r from-[#FFE000] to-[#FFC200] hover:opacity-90 transition-opacity cursor-pointer"
              >
                Send Reset Link
              </button>
            </form>
            <div className="mt-4 text-center text-sm text-gray-500">
              <Link href="/login" className="hover:text-gray-700">
                Back to Sign In
              </Link>
            </div>
          </>
        )}
      </div>
    </div>
  );
}
