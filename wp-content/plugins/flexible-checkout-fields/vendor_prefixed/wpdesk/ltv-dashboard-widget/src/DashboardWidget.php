<?php

namespace FcfVendor\WPDesk\Dashboard;

final class DashboardWidget
{
    const ID = 'flexible-checkout-fields';
    const MUTEX_HOOK = 'wpdesk/ltvdashboard/initialized';
    public function hooks()
    {
        if (\apply_filters(self::MUTEX_HOOK, \false) === \false) {
            \add_filter(self::MUTEX_HOOK, '__return_true');
            \add_action('wp_dashboard_setup', [$this, 'add_widget']);
        }
    }
    public function add_widget()
    {
        \wp_add_dashboard_widget(self::ID, \__('Expand your business with WP Desk', 'flexible-checkout-fields'), [$this, 'widget_output'], null, null, 'normal', 'high');
    }
    private function get_all_plugins_dirs() : array
    {
        $all_plugins = \array_keys(\get_plugins());
        return \array_map('dirname', $all_plugins);
    }
    private function filter_plugins_to_show(array $plugins) : array
    {
        \usort($plugins, static function ($a, $b) {
            return \strnatcmp($a['priority'], $b['priority']);
        });
        $installed_plugins_dir = $this->get_all_plugins_dirs();
        $plugins = \array_filter($plugins, static function ($plugin) use($installed_plugins_dir) {
            return !\in_array($plugin['slug'], $installed_plugins_dir, \true);
        });
        return \array_slice($plugins, 0, 3);
    }
    private function get_server() : string
    {
        $locale = \get_user_locale();
        if ($locale === 'pl_PL') {
            return 'www.wpdesk.pl';
        }
        return 'www.wpdesk.net';
    }
    private function get_utm_base() : string
    {
        return 'utm_source=dashboard-metabox&utm_campaign=dashboard-metabox';
    }
    private function get_widget_data() : array
    {
        $cache_key = \sprintf('wpdesk_ltv_%1$s_%2$s', self::ID, \get_user_locale());
        $cache_data = \get_transient($cache_key);
        if ($cache_data) {
            return $cache_data;
        } elseif ($cache_data === \false) {
            $response_data = $this->get_widget_data_from_remote();
            if ($response_data !== null) {
                \set_transient($cache_key, $response_data, 24 * 60 * 60);
                return $response_data;
            } else {
                \set_transient($cache_key, null, 6 * 60 * 60);
            }
        }
        return [];
    }
    /**
     * @return array|null
     */
    private function get_widget_data_from_remote()
    {
        $response = \wp_remote_get(\sprintf('https://%s?wpdesk_api=1&t=1', $this->get_server()), ['timeout' => 10, 'sslverify' => \false]);
        if (!\is_array($response)) {
            return null;
        }
        $ret = \json_decode($response['body'], \true);
        if (!$ret || !\is_array($ret)) {
            return null;
        }
        return ['header' => $ret['header'] ?? null, 'plugins' => $this->filter_plugins_to_show($ret['plugins'] ?? []), 'footer' => $ret['footer'] ?? null];
    }
    public function widget_output()
    {
        $widget_data = $this->get_widget_data();
        $server = $this->get_server();
        $utm_base = $this->get_utm_base();
        if (!empty($widget_data)) {
            echo '<div class="wpdesk_ltv_dashboard_widget">';
            if ($widget_data['header']) {
                echo \wp_kses_post($widget_data['header']);
            }
            echo '<ul class="ltv-rows">';
            foreach ($widget_data['plugins'] as $plugin) {
                $plugin_url = \sprintf('%1$s?%2$s&utm_medium=more-info-button&utm_term=%3$s', $plugin['url'], $utm_base, $plugin['slug']);
                $add_to_cart_url = \sprintf('https://%1$s/?add-to-cart=%2$s&%3$s&utm_medium=buy-now-button&utm_term=%4$s', $server, $plugin['add_to_cart_id'], $utm_base, $plugin['slug']);
                echo '<li class="ltv-row">';
                if ($plugin['image']) {
                    echo '<img src="' . \esc_url($plugin['image']) . '" alt="" />';
                }
                echo '<p><strong>' . \esc_html($plugin['name']) . '</strong></p>';
                echo '<div class="ltv-row-description">' . \wp_kses_post($plugin['description']) . '</div>';
                echo '<div class="ltv-buttons">';
                echo '<a class="button button-primary button-large" href="' . \esc_url($plugin_url) . '" target="_blank">' . \esc_html__('More info', 'flexible-checkout-fields') . '</a>';
                echo '&nbsp;';
                echo '<a class="button button-large" href="' . \esc_url($add_to_cart_url) . '" target="_blank">' . \esc_html__('Buy now', 'flexible-checkout-fields') . '</a>';
                echo '</div>';
                echo '</li>';
            }
            echo '</ul>';
            echo '<div class="ltv-footer">';
            if ($widget_data['footer']) {
                echo \wp_kses_post($widget_data['footer']);
            }
            echo '</div>';
            echo '</div>';
            ?>
            <style>
                .wpdesk_ltv_dashboard_widget .ltv-rows {
                    margin-left: -12px;
                    margin-right: -12px;
                }

                .wpdesk_ltv_dashboard_widget .ltv-row {
                    padding: 6px 12px 24px;
                }

                .wpdesk_ltv_dashboard_widget .ltv-row:nth-child(odd) {
                    background-color: #f6f7f7;
                }

                .wpdesk_ltv_dashboard_widget .ltv-row-description p {
                    margin-top: 6px;
                }

                .wpdesk_ltv_dashboard_widget img {
                    display: block;
                    margin: 0 auto 10px;
                    width: 250px;
                    max-width: 100%;
                }

                .wpdesk_ltv_dashboard_widget .ltv-buttons {
                    display: flex;
                    justify-content: space-around;
                }

                .wpdesk_ltv_dashboard_widget .ltv-footer {
                    margin: 0 -12px;
                    padding: 0 12px;
                }

                .wpdesk_ltv_dashboard_widget .ltv-footer p {
                    margin: 0;
                }
            </style>
            <?php 
        }
    }
}
