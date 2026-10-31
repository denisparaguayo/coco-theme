<?php
/**
 * Gestor de actualizaciones automáticas y manuales del tema directamente desde GitHub.
 *
 * @package Coco_Theme
 */

namespace Coco_Theme;

defined( 'ABSPATH' ) || exit;

class Updater {

	const DEFAULT_REPO   = 'denisparaguayo/coco-theme';
	const DEFAULT_BRANCH = 'main';
	const THEME_SLUG     = 'coco-theme';
	const OPTION_REPO    = 'coco_theme_github_repo';
	const OPTION_BRANCH  = 'coco_theme_github_branch';
	const TRANSIENT_KEY  = 'coco_theme_github_version';

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_admin_page' ) );
		add_action( 'admin_init', array( $this, 'process_admin_actions' ) );
		add_filter( 'pre_set_site_transient_update_themes', array( $this, 'check_wp_theme_updates' ) );
		add_filter( 'upgrader_source_selection', array( $this, 'fix_update_dir' ), 10, 4 );
	}

	/**
	 * Devuelve el repositorio configurado (owner/repo).
	 */
	public function get_repo() {
		$repo = get_option( self::OPTION_REPO, self::DEFAULT_REPO );
		return ! empty( $repo ) ? trim( $repo ) : self::DEFAULT_REPO;
	}

	/**
	 * Devuelve la rama configurada.
	 */
	public function get_branch() {
		$branch = get_option( self::OPTION_BRANCH, self::DEFAULT_BRANCH );
		return ! empty( $branch ) ? trim( $branch ) : self::DEFAULT_BRANCH;
	}

	/**
	 * Devuelve la versión local actual instalada del tema.
	 */
	public function get_local_version() {
		$theme = wp_get_theme( self::THEME_SLUG );
		if ( $theme->exists() ) {
			return $theme->get( 'Version' );
		}
		return wp_get_theme()->get( 'Version' );
	}

	/**
	 * URL del ZIP de la rama en GitHub.
	 */
	public function get_zip_url() {
		return sprintf(
			'https://github.com/%s/archive/refs/heads/%s.zip',
			$this->get_repo(),
			$this->get_branch()
		);
	}

	/**
	 * Obtiene la versión remota de style.css en GitHub (soporta raíz o subcarpeta coco-theme/).
	 *
	 * @param bool $force Ignorar caché.
	 * @return string|false
	 */
	public function get_remote_version( $force = false ) {
		if ( ! $force ) {
			$cached = get_transient( self::TRANSIENT_KEY );
			if ( false !== $cached ) {
				return $cached;
			}
		}

		$repo   = $this->get_repo();
		$branch = $this->get_branch();

		$candidate_urls = array(
			sprintf( 'https://api.github.com/repos/%s/contents/style.css?ref=%s', $repo, $branch ),
			sprintf( 'https://raw.githubusercontent.com/%s/%s/style.css', $repo, $branch ),
			sprintf( 'https://raw.githubusercontent.com/%s/%s/%s/style.css', $repo, $branch, self::THEME_SLUG ),
		);

		foreach ( $candidate_urls as $raw_url ) {
			$headers = array(
				'Cache-Control' => 'no-cache, no-store, must-revalidate',
				'Pragma'        => 'no-cache',
			);
			if ( false !== strpos( $raw_url, 'api.github.com' ) ) {
				$headers['Accept'] = 'application/vnd.github.v3.raw';
			}

			$response = wp_remote_get(
				add_query_arg( 't', time(), $raw_url ),
				array(
					'timeout'    => 15,
					'user-agent' => 'Coco-Theme-Updater/' . $this->get_local_version() . '; ' . home_url(),
					'headers'    => $headers,
				)
			);

			if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
				continue;
			}

			$body = wp_remote_retrieve_body( $response );
			if ( preg_match( '/Version:\s*([0-9\.]+)/i', $body, $matches ) ) {
				$version = trim( $matches[1] );
				set_transient( self::TRANSIENT_KEY, $version, 2 * HOUR_IN_SECONDS );
				return $version;
			}
		}

		return false;
	}

	/**
	 * Registra el submenú en Apariencia > Actualizar Coco Theme.
	 */
	public function register_admin_page() {
		add_theme_page(
			__( 'Actualizar Coco Theme (GitHub)', 'coco-theme' ),
			__( 'Actualizar Coco Theme', 'coco-theme' ),
			'update_themes',
			'coco-theme-updater',
			array( $this, 'render_admin_page' )
		);
	}

	/**
	 * Procesa las acciones de comprobación, guardado de repo o actualización en 1 clic.
	 */
	public function process_admin_actions() {
		if ( ! isset( $_POST['coco_theme_github_action'] ) ) {
			return;
		}

		if ( ! check_admin_referer( 'coco_theme_github_nonce_action', 'coco_theme_github_nonce' ) ) {
			wp_die( esc_html__( 'Nonce de seguridad inválido.', 'coco-theme' ) );
		}

		if ( ! current_user_can( 'update_themes' ) ) {
			wp_die( esc_html__( 'No tienes permisos suficientes para actualizar temas.', 'coco-theme' ) );
		}

		$action = sanitize_text_field( wp_unslash( $_POST['coco_theme_github_action'] ) );

		if ( 'save_repo' === $action ) {
			$repo   = isset( $_POST['github_repo'] ) ? sanitize_text_field( wp_unslash( $_POST['github_repo'] ) ) : self::DEFAULT_REPO;
			$branch = isset( $_POST['github_branch'] ) ? sanitize_text_field( wp_unslash( $_POST['github_branch'] ) ) : self::DEFAULT_BRANCH;
			update_option( self::OPTION_REPO, $repo );
			update_option( self::OPTION_BRANCH, $branch );
			delete_transient( self::TRANSIENT_KEY );

			wp_safe_redirect(
				add_query_arg(
					array(
						'page'  => 'coco-theme-updater',
						'saved' => '1',
					),
					admin_url( 'themes.php' )
				)
			);
			exit;
		}

		if ( 'check' === $action ) {
			delete_transient( self::TRANSIENT_KEY );
			delete_site_transient( 'update_themes' );
			wp_clean_themes_cache();
			$this->get_remote_version( true );

			wp_safe_redirect(
				add_query_arg(
					array(
						'page'    => 'coco-theme-updater',
						'checked' => '1',
					),
					admin_url( 'themes.php' )
				)
			);
			exit;
		}

		if ( 'update' === $action ) {
			$result = $this->perform_update();

			if ( is_wp_error( $result ) ) {
				wp_safe_redirect(
					add_query_arg(
						array(
							'page'  => 'coco-theme-updater',
							'error' => rawurlencode( $result->get_error_message() ),
						),
						admin_url( 'themes.php' )
					)
				);
				exit;
			}

			delete_transient( self::TRANSIENT_KEY );
			delete_site_transient( 'update_themes' );

			wp_safe_redirect(
				add_query_arg(
					array(
						'page'    => 'coco-theme-updater',
						'updated' => '1',
						'new_ver' => rawurlencode( $result ),
					),
					admin_url( 'themes.php' )
				)
			);
			exit;
		}
	}

	/**
	 * Descarga el ZIP desde GitHub y actualiza los archivos del tema activo.
	 *
	 * @return string|\WP_Error
	 */
	public function perform_update() {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/misc.php';

		\WP_Filesystem();
		global $wp_filesystem;

		if ( ! is_object( $wp_filesystem ) ) {
			return new \WP_Error( 'fs_error', __( 'No se pudo inicializar el sistema de archivos de WordPress.', 'coco-theme' ) );
		}

		$temp_zip = download_url( $this->get_zip_url(), 300 );
		if ( is_wp_error( $temp_zip ) ) {
			return new \WP_Error( 'download_failed', sprintf( __( 'Error al descargar desde GitHub: %s', 'coco-theme' ), $temp_zip->get_error_message() ) );
		}

		$temp_dir = get_temp_dir() . 'coco_theme_update_' . time() . '/';
		$unzipped = unzip_file( $temp_zip, $temp_dir );
		@unlink( $temp_zip );

		if ( is_wp_error( $unzipped ) ) {
			return new \WP_Error( 'unzip_failed', sprintf( __( 'Error al descomprimir el paquete: %s', 'coco-theme' ), $unzipped->get_error_message() ) );
		}

		// Localizar la carpeta que contiene style.css y theme.json
		$source_dir = '';
		$iterator   = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator( $temp_dir, \RecursiveDirectoryIterator::SKIP_DOTS )
		);

		foreach ( $iterator as $item ) {
			if ( 'style.css' === $item->getFilename() && file_exists( dirname( $item->getPathname() ) . '/theme.json' ) ) {
				$source_dir = dirname( $item->getPathname() );
				break;
			}
		}

		if ( empty( $source_dir ) || ! is_dir( $source_dir ) ) {
			$wp_filesystem->delete( $temp_dir, true );
			return new \WP_Error( 'invalid_theme', __( 'No se encontraron style.css y theme.json dentro del repositorio de GitHub.', 'coco-theme' ) );
		}

		$destination = rtrim( get_template_directory(), '/\\' );
		$copy_result = copy_dir( $source_dir, $destination );

		$wp_filesystem->delete( $temp_dir, true );

		if ( is_wp_error( $copy_result ) ) {
			return new \WP_Error( 'copy_failed', sprintf( __( 'Error al copiar archivos del tema: %s', 'coco-theme' ), $copy_result->get_error_message() ) );
		}

		wp_clean_themes_cache();

		$new_ver   = $this->get_local_version();
		$style_php = $destination . '/style.css';
		if ( file_exists( $style_php ) ) {
			$content = file_get_contents( $style_php );
			if ( preg_match( '/Version:\s*([0-9\.]+)/i', $content, $m ) ) {
				$new_ver = trim( $m[1] );
			}
		}

		return $new_ver;
	}

	/**
	 * Integra el chequeo con el sistema nativo de actualizaciones de temas de WordPress.
	 */
	public function check_wp_theme_updates( $transient ) {
		if ( empty( $transient->checked ) ) {
			return $transient;
		}

		$local_ver  = $this->get_local_version();
		$remote_ver = $this->get_remote_version();

		if ( $remote_ver && version_compare( $local_ver, $remote_ver, '<' ) ) {
			$transient->response[ self::THEME_SLUG ] = array(
				'theme'       => self::THEME_SLUG,
				'new_version' => $remote_ver,
				'url'         => 'https://github.com/' . $this->get_repo(),
				'package'     => $this->get_zip_url(),
			);
		}

		return $transient;
	}

	/**
	 * Normaliza el nombre de la carpeta al actualizar desde el actualizador nativo de WP.
	 */
	public function fix_update_dir( $source, $remote_source, $upgrader, $hook_extra = array() ) {
		if ( isset( $hook_extra['theme'] ) && self::THEME_SLUG === $hook_extra['theme'] ) {
			$corrected = trailingslashit( $remote_source ) . self::THEME_SLUG . '/';
			if ( is_dir( $source . '/' . self::THEME_SLUG ) ) {
				return trailingslashit( $source . '/' . self::THEME_SLUG );
			}
			global $wp_filesystem;
			if ( is_object( $wp_filesystem ) && $source !== $corrected ) {
				if ( $wp_filesystem->move( $source, $corrected, true ) ) {
					return $corrected;
				}
			}
		}
		return $source;
	}

	/**
	 * Renderiza la pantalla en Apariencia > Actualizar Coco Theme.
	 */
	public function render_admin_page() {
		$local_ver  = $this->get_local_version();
		$remote_ver = $this->get_remote_version();
		$repo       = $this->get_repo();
		$branch     = $this->get_branch();
		$has_update = $remote_ver && version_compare( $local_ver, $remote_ver, '<' );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Actualizador de Coco Cherry Theme (GitHub)', 'coco-theme' ); ?></h1>

			<?php if ( isset( $_GET['updated'] ) ) : ?>
				<div class="notice notice-success is-dismissible">
					<p>
						<strong><?php esc_html_e( '¡Tema actualizado correctamente desde GitHub!', 'coco-theme' ); ?></strong>
						<?php
						if ( isset( $_GET['new_ver'] ) ) {
							printf( ' ' . esc_html__( 'Versión actual: %s', 'coco-theme' ), esc_html( sanitize_text_field( wp_unslash( $_GET['new_ver'] ) ) ) );
						}
						?>
					</p>
				</div>
			<?php endif; ?>

			<?php if ( isset( $_GET['checked'] ) ) : ?>
				<div class="notice notice-info is-dismissible">
					<p><?php esc_html_e( 'Comprobación con GitHub completada.', 'coco-theme' ); ?></p>
				</div>
			<?php endif; ?>

			<?php if ( isset( $_GET['saved'] ) ) : ?>
				<div class="notice notice-success is-dismissible">
					<p><?php esc_html_e( 'Configuración del repositorio guardada.', 'coco-theme' ); ?></p>
				</div>
			<?php endif; ?>

			<?php if ( isset( $_GET['error'] ) ) : ?>
				<div class="notice notice-error is-dismissible">
					<p><strong><?php esc_html_e( 'Error:', 'coco-theme' ); ?></strong> <?php echo esc_html( sanitize_text_field( wp_unslash( $_GET['error'] ) ) ); ?></p>
				</div>
			<?php endif; ?>

			<div class="card" style="max-width: 680px; padding: 20px; margin-top: 20px;">
				<h2 style="margin-top:0;"><?php esc_html_e( 'Estado de Versión', 'coco-theme' ); ?></h2>
				<table class="widefat striped" style="margin-bottom: 20px;">
					<tbody>
						<tr>
							<td><strong><?php esc_html_e( 'Versión instalada en este sitio:', 'coco-theme' ); ?></strong></td>
							<td><code>v<?php echo esc_html( $local_ver ); ?></code></td>
						</tr>
						<tr>
							<td><strong><?php esc_html_e( 'Versión disponible en GitHub:', 'coco-theme' ); ?></strong></td>
							<td>
								<?php if ( $remote_ver ) : ?>
									<code>v<?php echo esc_html( $remote_ver ); ?></code>
									<?php if ( $has_update ) : ?>
										<span style="color:#16a34a; font-weight:700; margin-left:8px;"><?php esc_html_e( '¡Nueva versión disponible!', 'coco-theme' ); ?></span>
									<?php else : ?>
										<span style="color:#52606d; margin-left:8px;"><?php esc_html_e( '(Al día)', 'coco-theme' ); ?></span>
									<?php endif; ?>
								<?php else : ?>
									<span style="color:#d97706;"><?php esc_html_e( 'No detectada aún (verifica que el repositorio sea público y tenga style.css)', 'coco-theme' ); ?></span>
								<?php endif; ?>
							</td>
						</tr>
						<tr>
							<td><strong><?php esc_html_e( 'Repositorio conectado:', 'coco-theme' ); ?></strong></td>
							<td>
								<a href="<?php echo esc_url( 'https://github.com/' . $repo ); ?>" target="_blank" rel="noopener">
									https://github.com/<?php echo esc_html( $repo ); ?> (<?php echo esc_html( $branch ); ?>)
								</a>
							</td>
						</tr>
					</tbody>
				</table>

				<div style="display:flex; gap: 12px; flex-wrap: wrap;">
					<form method="post">
						<?php wp_nonce_field( 'coco_theme_github_nonce_action', 'coco_theme_github_nonce' ); ?>
						<input type="hidden" name="coco_theme_github_action" value="check" />
						<button type="submit" class="button button-secondary">
							<?php esc_html_e( 'Comprobar versión en GitHub', 'coco-theme' ); ?>
						</button>
					</form>

					<form method="post">
						<?php wp_nonce_field( 'coco_theme_github_nonce_action', 'coco_theme_github_nonce' ); ?>
						<input type="hidden" name="coco_theme_github_action" value="update" />
						<button type="submit" class="button button-primary">
							<?php esc_html_e( 'Forzar Descarga y Actualizar desde GitHub Ahora', 'coco-theme' ); ?>
						</button>
					</form>
				</div>
				<p class="description" style="margin-top: 12px;">
					<?php esc_html_e( 'Nota: El botón "Forzar Descarga y Actualizar" descargará el último código de la rama configurada en GitHub y reemplazará los archivos del tema aunque no hayas cambiado el número de versión.', 'coco-theme' ); ?>
				</p>
			</div>

			<div class="card" style="max-width: 680px; padding: 20px; margin-top: 20px;">
				<h2 style="margin-top:0;"><?php esc_html_e( 'Configuración del Repositorio GitHub', 'coco-theme' ); ?></h2>
				<form method="post">
					<?php wp_nonce_field( 'coco_theme_github_nonce_action', 'coco_theme_github_nonce' ); ?>
					<input type="hidden" name="coco_theme_github_action" value="save_repo" />
					<table class="form-table">
						<tr>
							<th scope="row"><label for="github_repo"><?php esc_html_e( 'Repositorio (usuario/repo)', 'coco-theme' ); ?></label></th>
							<td>
								<input type="text" id="github_repo" name="github_repo" value="<?php echo esc_attr( $repo ); ?>" class="regular-text" placeholder="denisparaguayo/coco-theme" />
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="github_branch"><?php esc_html_e( 'Rama (Branch)', 'coco-theme' ); ?></label></th>
							<td>
								<input type="text" id="github_branch" name="github_branch" value="<?php echo esc_attr( $branch ); ?>" class="regular-text" placeholder="main" />
							</td>
						</tr>
					</table>
					<button type="submit" class="button button-secondary"><?php esc_html_e( 'Guardar Repositorio', 'coco-theme' ); ?></button>
				</form>
			</div>
		</div>
		<?php
	}
}

