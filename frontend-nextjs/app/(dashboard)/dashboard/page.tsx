import React from 'react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { 
  BarChart3, 
  Database, 
  Users, 
  Calendar, 
  Download,
  Plus,
  FileText,
  Settings,
  TrendingUp,
  Activity,
  Clock
} from 'lucide-react';

interface StatsCardProps {
  title: string;
  value: string | number;
  description: string;
  icon: React.ReactNode;
  trend?: {
    value: number;
    isPositive: boolean;
  };
}

interface RecentActivityItem {
  id: string;
  type: 'report_created' | 'report_exported' | 'data_source_added' | 'report_scheduled';
  title: string;
  user: string;
  timestamp: string;
  status: 'success' | 'pending' | 'error';
}

const StatsCard: React.FC<StatsCardProps> = ({ title, value, description, icon, trend }) => {
  return (
    <Card className="hover:shadow-md transition-shadow">
      <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
        <CardTitle className="text-sm font-medium text-muted-foreground">
          {title}
        </CardTitle>
        <div className="text-muted-foreground">
          {icon}
        </div>
      </CardHeader>
      <CardContent>
        <div className="text-2xl font-bold">{value}</div>
        <div className="flex items-center justify-between mt-2">
          <p className="text-xs text-muted-foreground">
            {description}
          </p>
          {trend && (
            <div className={`flex items-center text-xs ${
              trend.isPositive ? 'text-green-600' : 'text-red-600'
            }`}>
              <TrendingUp className={`w-3 h-3 mr-1 ${
                trend.isPositive ? '' : 'rotate-180'
              }`} />
              {Math.abs(trend.value)}%
            </div>
          )}
        </div>
      </CardContent>
    </Card>
  );
};

const QuickActionButton: React.FC<{
  icon: React.ReactNode;
  title: string;
  description: string;
  onClick: () => void;
}> = ({ icon, title, description, onClick }) => {
  return (
    <Button
      variant="outline"
      className="h-auto p-4 flex flex-col items-start space-y-2 hover:bg-accent"
      onClick={onClick}
    >
      <div className="flex items-center space-x-2">
        {icon}
        <span className="font-medium">{title}</span>
      </div>
      <span className="text-xs text-muted-foreground text-left">
        {description}
      </span>
    </Button>
  );
};

const ActivityItem: React.FC<{ activity: RecentActivityItem }> = ({ activity }) => {
  const getStatusBadge = (status: string) => {
    switch (status) {
      case 'success':
        return <Badge variant="default" className="bg-green-100 text-green-800">Success</Badge>;
      case 'pending':
        return <Badge variant="secondary">Pending</Badge>;
      case 'error':
        return <Badge variant="destructive">Error</Badge>;
      default:
        return <Badge variant="outline">Unknown</Badge>;
    }
  };

  const getActivityIcon = (type: string) => {
    switch (type) {
      case 'report_created':
        return <FileText className="w-4 h-4" />;
      case 'report_exported':
        return <Download className="w-4 h-4" />;
      case 'data_source_added':
        return <Database className="w-4 h-4" />;
      case 'report_scheduled':
        return <Calendar className="w-4 h-4" />;
      default:
        return <Activity className="w-4 h-4" />;
    }
  };

  return (
    <div className="flex items-center justify-between py-3 border-b last:border-b-0">
      <div className="flex items-center space-x-3">
        <div className="text-muted-foreground">
          {getActivityIcon(activity.type)}
        </div>
        <div className="flex-1 min-w-0">
          <p className="text-sm font-medium text-gray-900 truncate">
            {activity.title}
          </p>
          <div className="flex items-center space-x-2 mt-1">
            <p className="text-xs text-muted-foreground">
              by {activity.user}
            </p>
            <span className="text-xs text-muted-foreground">•</span>
            <p className="text-xs text-muted-foreground flex items-center">
              <Clock className="w-3 h-3 mr-1" />
              {activity.timestamp}
            </p>
          </div>
        </div>
      </div>
      <div className="flex items-center space-x-2">
        {getStatusBadge(activity.status)}
      </div>
    </div>
  );
};

export default function DashboardPage() {
  // Mock data - in a real app, this would come from API calls
  const stats = [
    {
      title: "Total Reports",
      value: 247,
      description: "+12% from last month",
      icon: <BarChart3 className="h-4 w-4" />,
      trend: { value: 12, isPositive: true }
    },
    {
      title: "Active Data Sources",
      value: 18,
      description: "3 added this week",
      icon: <Database className="h-4 w-4" />,
      trend: { value: 8, isPositive: true }
    },
    {
      title: "Reports by User",
      value: 42,
      description: "Average per user",
      icon: <Users className="h-4 w-4" />
    },
    {
      title: "Scheduled Reports",
      value: 15,
      description: "Next run in 2 hours",
      icon: <Calendar className="h-4 w-4" />
    },
    {
      title: "Monthly Exports",
      value: "1.2K",
      description: "+5% from last month",
      icon: <Download className="h-4 w-4" />,
      trend: { value: 5, isPositive: true }
    }
  ];

  const recentActivity: RecentActivityItem[] = [
    {
      id: '1',
      type: 'report_created',
      title: 'Sales Performance Q4 Report',
      user: 'John Doe',
      timestamp: '2 minutes ago',
      status: 'success'
    },
    {
      id: '2',
      type: 'report_exported',
      title: 'Customer Analytics Dashboard',
      user: 'Sarah Wilson',
      timestamp: '15 minutes ago',
      status: 'success'
    },
    {
      id: '3',
      type: 'data_source_added',
      title: 'PostgreSQL Production DB',
      user: 'Mike Johnson',
      timestamp: '1 hour ago',
      status: 'pending'
    },
    {
      id: '4',
      type: 'report_scheduled',
      title: 'Weekly Revenue Report',
      user: 'Emily Brown',
      timestamp: '2 hours ago',
      status: 'success'
    },
    {
      id: '5',
      type: 'report_exported',
      title: 'Inventory Analysis',
      user: 'David Lee',
      timestamp: '3 hours ago',
      status: 'error'
    }
  ];

  const handleQuickAction = (action: string) => {
    console.log(`Quick action: ${action}`);
    // Implement navigation or modal opening logic here
  };

  return (
    <div className="flex-1 space-y-6 p-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold tracking-tight">Dashboard</h1>
          <p className="text-muted-foreground mt-1">
            Welcome back! Here's what's happening with your reports.
          </p>
        </div>
        <Button onClick={() => handleQuickAction('create-report')}>
          <Plus className="w-4 h-4 mr-2" />
          New Report
        </Button>
      </div>

      {/* Stats Grid */}
      <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-5">
        {stats.map((stat, index) => (
          <StatsCard
            key={index}
            title={stat.title}
            value={stat.value}
            description={stat.description}
            icon={stat.icon}
            trend={stat.trend}
          />
        ))}
      </div>

      {/* Main Content Grid */}
      <div className="grid gap-6 lg:grid-cols-3">
        {/* Quick Actions */}
        <Card className="lg:col-span-1">
          <CardHeader>
            <CardTitle className="flex items-center space-x-2">
              <Settings className="w-5 h-5" />
              <span>Quick Actions</span>
            </CardTitle>
            <CardDescription>
              Common tasks and shortcuts
            </CardDescription>
          </CardHeader>
          <CardContent className="space-y-3">
            <QuickActionButton
              icon={<Plus className="w-4 h-4" />}
              title="Create Report"
              description="Build a new report from scratch"
              onClick={() => handleQuickAction('create-report')}
            />
            <QuickActionButton
              icon={<Database className="w-4 h-4" />}
              title="Add Data Source"
              description="Connect a new database or API"
              onClick={() => handleQuickAction('add-data-source')}
            />
            <QuickActionButton
              icon={<Calendar className="w-4 h-4" />}
              title="Schedule Report"
              description="Set up automated reporting"
              onClick={() => handleQuickAction('schedule-report')}
            />
            <QuickActionButton
              icon={<BarChart3 className="w-4 h-4" />}
              title="View Analytics"
              description="Check usage and performance"
              onClick={() => handleQuickAction('view-analytics')}
            />
          </CardContent>
        </Card>

        {/* Recent Activity */}
        <Card className="lg:col-span-2">
          <CardHeader>
            <CardTitle className="flex items-center space-x-2">
              <Activity className="w-5 h-5" />
              <span>Recent Activity</span>
            </CardTitle>
            <CardDescription>
              Latest actions across your workspace
            </CardDescription>
          </CardHeader>
          <CardContent>
            <div className="space-y-1">
              {recentActivity.map((activity) => (
                <ActivityItem key={activity.id} activity={activity} />
              ))}
            </div>
            <div className="mt-4 pt-4 border-t">
              <Button 
                variant="ghost" 
                className="w-full"
                onClick={() => handleQuickAction('view-all-activity')}
              >
                View All Activity
              </Button>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  );
}