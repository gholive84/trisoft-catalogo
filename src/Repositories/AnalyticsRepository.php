<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class AnalyticsRepository
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::connection();
    }

    public function recordPageView(array $data): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO page_views
                (user_id, session_id, url, referrer, user_agent, ip_address, product_id, category_id)
             VALUES (:uid, :sid, :url, :ref, :ua, :ip, :pid, :cid)"
        );
        $stmt->execute([
            'uid' => $data['user_id'] ?? null,
            'sid' => (string) ($data['session_id'] ?? ''),
            'url' => mb_substr((string) ($data['url'] ?? ''), 0, 500),
            'ref' => mb_substr((string) ($data['referrer'] ?? ''), 0, 500),
            'ua'  => mb_substr((string) ($data['user_agent'] ?? ''), 0, 500),
            'ip'  => mb_substr((string) ($data['ip_address'] ?? ''), 0, 45),
            'pid' => $data['product_id'] ?? null,
            'cid' => $data['category_id'] ?? null,
        ]);
    }

    public function recordEvent(string $name, array $data = [], ?int $userId = null, string $sessionId = '', ?string $url = null): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO events (user_id, session_id, event_name, event_data, url)
             VALUES (:uid, :sid, :name, :data, :url)"
        );
        $stmt->execute([
            'uid'  => $userId,
            'sid'  => $sessionId,
            'name' => mb_substr($name, 0, 80),
            'data' => $data === [] ? null : json_encode($data, JSON_UNESCAPED_UNICODE),
            'url'  => $url !== null ? mb_substr($url, 0, 500) : null,
        ]);
    }

    /* ---------------- KPIs ---------------- */

    public function visitorsCount(int $days = 30): int
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(DISTINCT session_id) FROM page_views
              WHERE created_at >= NOW() - INTERVAL :d DAY"
        );
        $stmt->execute(['d' => $days]);
        return (int) $stmt->fetchColumn();
    }

    public function pageViewsCount(int $days = 30): int
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM page_views WHERE created_at >= NOW() - INTERVAL :d DAY"
        );
        $stmt->execute(['d' => $days]);
        return (int) $stmt->fetchColumn();
    }

    public function loggedSessionsCount(int $days = 30): int
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(DISTINCT user_id) FROM page_views
              WHERE user_id IS NOT NULL AND created_at >= NOW() - INTERVAL :d DAY"
        );
        $stmt->execute(['d' => $days]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Funil: visitors → product_views → add_to_cart → quote_requested
     */
    public function funnel(int $days = 30): array
    {
        $visitors = $this->visitorsCount($days);

        $productViews = (int) $this->pdo->prepare(
            "SELECT COUNT(DISTINCT session_id) FROM page_views
              WHERE product_id IS NOT NULL AND created_at >= NOW() - INTERVAL :d DAY"
        )->execute(['d' => $days]) ?: 0;
        $st = $this->pdo->prepare(
            "SELECT COUNT(DISTINCT session_id) FROM page_views
              WHERE product_id IS NOT NULL AND created_at >= NOW() - INTERVAL :d DAY"
        );
        $st->execute(['d' => $days]);
        $productViews = (int) $st->fetchColumn();

        $st = $this->pdo->prepare(
            "SELECT COUNT(DISTINCT session_id) FROM events
              WHERE event_name = 'add_to_cart' AND created_at >= NOW() - INTERVAL :d DAY"
        );
        $st->execute(['d' => $days]);
        $addToCart = (int) $st->fetchColumn();

        $st = $this->pdo->prepare(
            "SELECT COUNT(*) FROM orders WHERE created_at >= NOW() - INTERVAL :d DAY"
        );
        $st->execute(['d' => $days]);
        $quotes = (int) $st->fetchColumn();

        return [
            'visitors'      => $visitors,
            'product_views' => $productViews,
            'add_to_cart'   => $addToCart,
            'quotes'        => $quotes,
        ];
    }

    /**
     * Top produtos visualizados.
     * @return array<int, array{product_id:int,name:string,slug:string,views:int}>
     */
    public function topProducts(int $days = 30, int $limit = 10): array
    {
        $sql = "SELECT p.id AS product_id, p.name, p.slug, COUNT(*) AS views
                  FROM page_views pv
                  JOIN products p ON p.id = pv.product_id
                 WHERE pv.product_id IS NOT NULL
                   AND pv.created_at >= NOW() - INTERVAL :d DAY
              GROUP BY p.id, p.name, p.slug
              ORDER BY views DESC
                 LIMIT {$limit}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['d' => $days]);
        return $stmt->fetchAll();
    }

    /** @return array<int, array{category_id:int,name:string,slug:string,views:int}> */
    public function topCategories(int $days = 30, int $limit = 10): array
    {
        $sql = "SELECT c.id AS category_id, c.name, c.slug, COUNT(*) AS views
                  FROM page_views pv
                  JOIN categories c ON c.id = pv.category_id
                 WHERE pv.category_id IS NOT NULL
                   AND pv.created_at >= NOW() - INTERVAL :d DAY
              GROUP BY c.id, c.name, c.slug
              ORDER BY views DESC
                 LIMIT {$limit}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['d' => $days]);
        return $stmt->fetchAll();
    }

    /** @return array<int, array{query:string,count:int,zero_results:bool}> */
    public function topSearches(int $days = 30, int $limit = 15): array
    {
        // events.event_data contém {query: "..."}
        $sql = "SELECT JSON_UNQUOTE(JSON_EXTRACT(event_data, '$.query')) AS query,
                       COUNT(*) AS count,
                       AVG(COALESCE(JSON_EXTRACT(event_data, '$.results'), 0)) AS avg_results
                  FROM events
                 WHERE event_name = 'search'
                   AND created_at >= NOW() - INTERVAL :d DAY
                   AND JSON_EXTRACT(event_data, '$.query') IS NOT NULL
              GROUP BY query
              ORDER BY count DESC
                 LIMIT {$limit}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['d' => $days]);
        $rows = $stmt->fetchAll();
        foreach ($rows as &$r) {
            $r['zero_results'] = ((float) $r['avg_results'] === 0.0);
        }
        return $rows;
    }

    /**
     * Tráfego diário (últimos N dias) → array de {date, pageviews, sessions}.
     * @return array<int, array{date:string,pageviews:int,sessions:int}>
     */
    public function dailyTraffic(int $days = 14): array
    {
        $sql = "SELECT DATE(created_at) AS date,
                       COUNT(*) AS pageviews,
                       COUNT(DISTINCT session_id) AS sessions
                  FROM page_views
                 WHERE created_at >= NOW() - INTERVAL :d DAY
              GROUP BY DATE(created_at)
              ORDER BY date ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['d' => $days]);
        $rows = $stmt->fetchAll();

        // Garante todos os dias da janela (preenche com zeros os sem dados)
        $byDate = [];
        foreach ($rows as $r) {
            $byDate[$r['date']] = $r;
        }
        $out = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-{$i} days"));
            $out[] = $byDate[$d] ?? ['date' => $d, 'pageviews' => 0, 'sessions' => 0];
        }
        return $out;
    }

    public function eventsCount(string $name, int $days = 30): int
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM events
              WHERE event_name = :n AND created_at >= NOW() - INTERVAL :d DAY"
        );
        $stmt->execute(['n' => $name, 'd' => $days]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Sessões ativas agora (atividade nos últimos N minutos).
     * Para visitantes anônimos: distintos session_id.
     * Para logados: agrupados por user_id.
     * @return array<int, array{user_id:?int,name:?string,email:?string,session_id:string,last_seen:string,pages:int}>
     */
    public function activeNow(int $minutes = 15, int $limit = 20): array
    {
        $sql = "SELECT
                    pv.session_id,
                    pv.user_id,
                    u.name,
                    u.email,
                    u.role,
                    MAX(pv.created_at) AS last_seen,
                    COUNT(*) AS pages,
                    (SELECT url FROM page_views WHERE session_id = pv.session_id ORDER BY created_at DESC LIMIT 1) AS last_url
                  FROM page_views pv
             LEFT JOIN users u ON u.id = pv.user_id
                 WHERE pv.created_at >= NOW() - INTERVAL :m MINUTE
              GROUP BY pv.session_id, pv.user_id, u.name, u.email, u.role
              ORDER BY last_seen DESC
                 LIMIT {$limit}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['m' => $minutes]);
        return $stmt->fetchAll();
    }

    /**
     * Últimas N sessões (visitantes ou usuários), independente de estarem online agora.
     * @return array<int, array>
     */
    public function recentVisitors(int $days = 30, int $limit = 30): array
    {
        $sql = "SELECT
                    pv.session_id,
                    pv.user_id,
                    u.name,
                    u.email,
                    u.role,
                    MIN(pv.created_at) AS first_seen,
                    MAX(pv.created_at) AS last_seen,
                    COUNT(*) AS pages,
                    (SELECT url FROM page_views WHERE session_id = pv.session_id ORDER BY created_at DESC LIMIT 1) AS last_url,
                    (SELECT ip_address FROM page_views WHERE session_id = pv.session_id ORDER BY created_at DESC LIMIT 1) AS ip_address
                  FROM page_views pv
             LEFT JOIN users u ON u.id = pv.user_id
                 WHERE pv.created_at >= NOW() - INTERVAL :d DAY
              GROUP BY pv.session_id, pv.user_id, u.name, u.email, u.role
              ORDER BY last_seen DESC
                 LIMIT {$limit}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['d' => $days]);
        return $stmt->fetchAll();
    }

    public function activeSessionsCount(int $minutes = 15): int
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(DISTINCT session_id) FROM page_views
              WHERE created_at >= NOW() - INTERVAL :m MINUTE"
        );
        $stmt->execute(['m' => $minutes]);
        return (int) $stmt->fetchColumn();
    }

    public function abandonedCartsCount(int $minDaysIdle = 3): int
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM carts c
              WHERE c.last_activity_at < NOW() - INTERVAL :d DAY
                AND EXISTS (SELECT 1 FROM cart_items ci WHERE ci.cart_id = c.id)"
        );
        $stmt->execute(['d' => $minDaysIdle]);
        return (int) $stmt->fetchColumn();
    }
}
