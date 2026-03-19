import type { Metadata } from 'next'
import './globals.css'
export const metadata: Metadata = { title: 'ReportCraft', description: 'A versatile reporting tool that transforms raw data into professional, interactive reports with cust' }
export default function RootLayout({ children }: { children: React.ReactNode }) {
  return <html lang="en"><body>{children}</body></html>
}
