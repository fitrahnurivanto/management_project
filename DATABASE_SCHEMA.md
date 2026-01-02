# DATABASE SCHEMA & RELATIONSHIPS

## Entity Relationship Diagram (ERD)

```
┌─────────────────┐
│     USERS       │
├─────────────────┤
│ id (PK)         │
│ name            │
│ email           │
│ password        │
│ role            │◄────┐
│ phone           │     │
│ address         │     │
│ avatar          │     │
└─────────────────┘     │
         │              │
         │              │
         │ 1:1          │
         ▼              │
┌─────────────────┐     │
│    CLIENTS      │     │
├─────────────────┤     │
│ id (PK)         │     │
│ user_id (FK) ───┘     │
│ company_name    │     │
│ company_address │     │
│ business_type   │     │
│ npwp            │     │
│ contact_person  │     │
│ contact_phone   │     │
└─────────────────┘     │
         │              │
         │ 1:N          │
         ▼              │
┌─────────────────┐     │
│     ORDERS      │     │
├─────────────────┤     │
│ id (PK)         │     │
│ client_id (FK)  │     │
│ order_number    │     │
│ total_amount    │     │
│ payment_status  │     │
│ payment_method  │     │
│ payment_proof   │     │
│ confirmed_at    │     │
│ confirmed_by ───┼─────┘
└─────────────────┘
         │
         │ 1:N
         ▼
┌─────────────────┐
│  ORDER_ITEMS    │
├─────────────────┤
│ id (PK)         │
│ order_id (FK)   │
│ service_id (FK) │◄─────┐
│ quantity        │      │
│ price           │      │
│ subtotal        │      │
│ specifications  │      │
└─────────────────┘      │
                         │
                         │
┌─────────────────┐      │
│ SERVICE_CAT     │      │
├─────────────────┤      │
│ id (PK)         │      │
│ name            │      │
│ slug            │      │
│ description     │      │
│ icon            │      │
│ is_active       │      │
│ display_order   │      │
└─────────────────┘      │
         │               │
         │ 1:N           │
         ▼               │
┌─────────────────┐      │
│    SERVICES     │      │
├─────────────────┤      │
│ id (PK) ────────┼──────┘
│ category_id(FK) │
│ name            │
│ slug            │
│ description     │
│ base_price      │
│ features        │
│ is_active       │
│ display_order   │
└─────────────────┘
         │
         │ N:M
         ▼
┌─────────────────┐
│PROJECT_SERVICES │
├─────────────────┤
│ id (PK)         │
│ project_id (FK) │◄─────┐
│ service_id (FK) │      │
│ allocated_budget│      │
└─────────────────┘      │
                         │
                         │
┌─────────────────┐      │
│    PROJECTS     │      │
├─────────────────┤      │
│ id (PK) ────────┼──────┘
│ order_id (FK)   │
│ client_id (FK)  │
│ project_name    │
│ project_code    │
│ description     │
│ status          │
│ budget          │
│ actual_cost     │
│ start_date      │
│ end_date        │
│ completed_at    │
└─────────────────┘
         │
         ├── 1:N ──────►┌─────────────────┐
         │              │     TEAMS       │
         │              ├─────────────────┤
         │              │ id (PK)         │
         │              │ project_id (FK) │
         │              │ team_name       │
         │              │ description     │
         │              └─────────────────┘
         │                      │
         │                      │ 1:N
         │                      ▼
         │              ┌─────────────────┐
         │              │  TEAM_MEMBERS   │
         │              ├─────────────────┤
         │              │ id (PK)         │
         │              │ team_id (FK)    │
         │              │ user_id (FK) ───┼───┐
         │              │ role            │   │
         │              │ hourly_rate     │   │
         │              │ assigned_at     │   │
         │              └─────────────────┘   │
         │                                    │
         ├── 1:N ──────►┌─────────────────┐  │
         │              │PROJECT_TASKS    │  │
         │              ├─────────────────┤  │
         │              │ id (PK)         │  │
         │              │ project_id (FK) │  │
         │              │ assigned_to(FK) ├──┤
         │              │ title           │  │
         │              │ description     │  │
         │              │ status          │  │
         │              │ priority        │  │
         │              │ due_date        │  │
         │              └─────────────────┘  │
         │                      │            │
         │                      │ 1:N        │
         │                      ▼            │
         │              ┌─────────────────┐  │
         │              │TIME_TRACKINGS   │  │
         │              ├─────────────────┤  │
         │              │ id (PK)         │  │
         │              │ project_id (FK) │  │
         │              │ user_id (FK) ───┼──┤
         │              │ task_id (FK)    │  │
         │              │ description     │  │
         │              │ hours           │  │
         │              │ work_date       │  │
         │              └─────────────────┘  │
         │                                   │
         ├── 1:N ──────►┌─────────────────┐ │
         │              │PROJECT_EXPENSES │ │
         │              ├─────────────────┤ │
         │              │ id (PK)         │ │
         │              │ project_id (FK) │ │
         │              │ expense_type    │ │
         │              │ description     │ │
         │              │ amount          │ │
         │              │ expense_date    │ │
         │              │ receipt_file    │ │
         │              │ created_by (FK) ├─┤
         │              └─────────────────┘ │
         │                                  │
         └── 1:N ──────►┌─────────────────┐│
                        │PROJECT_MILESTON.││
                        ├─────────────────┤│
                        │ id (PK)         ││
                        │ project_id (FK) ││
                        │ title           ││
                        │ description     ││
                        │ target_date     ││
                        │ completed_at    ││
                        │ status          ││
                        └─────────────────┘│
                                           │
                        ┌─────────────────┐│
                        │ ACTIVITY_LOGS   ││
                        ├─────────────────┤│
                        │ id (PK)         ││
                        │ user_id (FK) ───┼┘
                        │ action          │
                        │ model           │
                        │ model_id        │
                        │ description     │
                        │ ip_address      │
                        └─────────────────┘
```

## Database Relationships

### Users
- **1:1** dengan Clients (user_id)
- **1:N** dengan Orders (confirmed_by)
- **1:N** dengan TeamMembers
- **1:N** dengan ProjectTasks (assigned_to)
- **1:N** dengan TimeTrackings
- **1:N** dengan ActivityLogs

### Clients
- **N:1** dengan Users
- **1:N** dengan Orders
- **1:N** dengan Projects

### Orders
- **N:1** dengan Clients
- **1:N** dengan OrderItems
- **1:1** dengan Projects

### Services
- **N:1** dengan ServiceCategories
- **1:N** dengan OrderItems
- **N:M** dengan Projects (through project_services)

### Projects
- **N:1** dengan Orders
- **N:1** dengan Clients
- **N:M** dengan Services (through project_services)
- **1:N** dengan Teams
- **1:N** dengan ProjectTasks
- **1:N** dengan ProjectMilestones
- **1:N** dengan ProjectExpenses
- **1:N** dengan TimeTrackings

### Teams
- **N:1** dengan Projects
- **1:N** dengan TeamMembers

### TeamMembers
- **N:1** dengan Teams
- **N:1** dengan Users

## Indexes untuk Performance

```sql
-- Users
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_users_email ON users(email);

-- Orders
CREATE INDEX idx_orders_client_id ON orders(client_id);
CREATE INDEX idx_orders_payment_status ON orders(payment_status);
CREATE INDEX idx_orders_order_number ON orders(order_number);

-- Projects
CREATE INDEX idx_projects_status ON projects(status);
CREATE INDEX idx_projects_client_id ON projects(client_id);
CREATE INDEX idx_projects_order_id ON projects(order_id);
CREATE INDEX idx_projects_project_code ON projects(project_code);

-- Team Members
CREATE INDEX idx_team_members_user_id ON team_members(user_id);
CREATE INDEX idx_team_members_team_id ON team_members(team_id);

-- Project Tasks
CREATE INDEX idx_project_tasks_project_id ON project_tasks(project_id);
CREATE INDEX idx_project_tasks_assigned_to ON project_tasks(assigned_to);
CREATE INDEX idx_project_tasks_status ON project_tasks(status);

-- Time Trackings
CREATE INDEX idx_time_trackings_project_id ON time_trackings(project_id);
CREATE INDEX idx_time_trackings_user_id ON time_trackings(user_id);
CREATE INDEX idx_time_trackings_work_date ON time_trackings(work_date);
```

## Calculated Fields

### Project Model
```php
// Profit = Budget - Actual Cost
$profit = $project->budget - $project->actual_cost;

// Profit Margin = (Profit / Budget) * 100
$profitMargin = ($profit / $project->budget) * 100;
```

### Actual Cost Calculation
```php
// Labor Cost
$laborCost = TimeTracking::where('project_id', $projectId)
    ->join('team_members', function($join) {
        $join->on('time_trackings.user_id', '=', 'team_members.user_id')
             ->on('time_trackings.project_id', '=', 'team_members.team_id');
    })
    ->sum(DB::raw('time_trackings.hours * team_members.hourly_rate'));

// Material/Expense Cost
$expenseCost = ProjectExpense::where('project_id', $projectId)
    ->sum('amount');

// Total Actual Cost
$actualCost = $laborCost + $expenseCost;
```

## Data Flow untuk Perhitungan Omset

```
┌─────────────────────────────────────────────────────────┐
│                    REVENUE CALCULATION                   │
└─────────────────────────────────────────────────────────┘
                            │
                            ▼
              ┌─────────────────────────┐
              │  Orders (payment_status │
              │      = 'paid')          │
              │  SUM(total_amount)      │
              └─────────────────────────┘
                            │
                            ▼
                    TOTAL REVENUE
                            
┌─────────────────────────────────────────────────────────┐
│                     COST CALCULATION                     │
└─────────────────────────────────────────────────────────┘
                            │
              ┌─────────────┴─────────────┐
              ▼                           ▼
    ┌──────────────────┐      ┌──────────────────┐
    │   LABOR COST     │      │  EXPENSE COST    │
    │                  │      │                  │
    │ TimeTrackings    │      │ ProjectExpenses  │
    │ × HourlyRate     │      │ SUM(amount)      │
    └──────────────────┘      └──────────────────┘
              │                           │
              └─────────────┬─────────────┘
                            ▼
                       TOTAL COST

┌─────────────────────────────────────────────────────────┐
│                    PROFIT CALCULATION                    │
└─────────────────────────────────────────────────────────┘
                            │
                            ▼
              PROFIT = REVENUE - COST
              
              PROFIT MARGIN = (PROFIT / REVENUE) × 100%
```

## Sample Queries untuk Reporting

### Total Omset Bulan Ini
```sql
SELECT 
    SUM(o.total_amount) as revenue,
    SUM(p.actual_cost) as cost,
    SUM(o.total_amount - p.actual_cost) as profit
FROM orders o
JOIN projects p ON o.id = p.order_id
WHERE o.payment_status = 'paid'
AND MONTH(o.confirmed_at) = MONTH(CURRENT_DATE())
AND YEAR(o.confirmed_at) = YEAR(CURRENT_DATE());
```

### Revenue by Category
```sql
SELECT 
    sc.name as category,
    SUM(oi.subtotal) as revenue
FROM service_categories sc
JOIN services s ON sc.id = s.category_id
JOIN order_items oi ON s.id = oi.service_id
JOIN orders o ON oi.order_id = o.id
WHERE o.payment_status = 'paid'
GROUP BY sc.name
ORDER BY revenue DESC;
```

### Top Profitable Projects
```sql
SELECT 
    p.project_name,
    p.budget,
    p.actual_cost,
    (p.budget - p.actual_cost) as profit,
    ((p.budget - p.actual_cost) / p.budget * 100) as profit_margin
FROM projects p
WHERE p.status IN ('completed', 'in_progress')
AND p.budget > 0
ORDER BY profit DESC
LIMIT 10;
```

---

**Note**: Diagram ini menunjukkan struktur database dan relationship antar tabel untuk sistem manajemen proyek.
