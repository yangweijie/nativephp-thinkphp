#![cfg_attr(
    all(not(debug_assertions), target_os = "windows"),
    windows_subsystem = "windows"
)]

use tauri::Manager;

#[derive(Clone, serde::Serialize)]
struct WindowOptions {
    label: String,
    options: serde_json::Value,
}

#[tauri::command]
fn create_window(app: tauri::AppHandle, label: String, options: serde_json::Value) -> Result<(), String> {
    let builder = tauri::WindowBuilder::new(&app, &label, tauri::WindowUrl::App("index.html".into()))
        .initialization_script(&format!(
            "window.__TAURI__.windowLabel = '{}';",
            label
        ));

    let window = match builder.build() {
        Ok(window) => window,
        Err(e) => return Err(e.to_string()),
    };

    // 应用窗口配置
    if let Some(options) = options.as_object() {
        if let Some(title) = options.get("title").and_then(|v| v.as_str()) {
            window.set_title(title).ok();
        }
        if let Some(width) = options.get("width").and_then(|v| v.as_f64()) {
            window.set_size(tauri::PhysicalSize::new(width as u32, window.inner_size().unwrap().height)).ok();
        }
        if let Some(height) = options.get("height").and_then(|v| v.as_f64()) {
            window.set_size(tauri::PhysicalSize::new(window.inner_size().unwrap().width, height as u32)).ok();
        }
        if let Some(fullscreen) = options.get("fullscreen").and_then(|v| v.as_bool()) {
            window.set_fullscreen(fullscreen).ok();
        }
        if let Some(decorations) = options.get("decorations").and_then(|v| v.as_bool()) {
            window.set_decorations(decorations).ok();
        }
        if let Some(always_on_top) = options.get("alwaysOnTop").and_then(|v| v.as_bool()) {
            window.set_always_on_top(always_on_top).ok();
        }
    }

    Ok(())
}

#[tauri::command]
fn close_window(window: tauri::Window) {
    window.close().ok();
}

#[tauri::command]
fn minimize_window(window: tauri::Window) {
    window.minimize().ok();
}

#[tauri::command]
fn maximize_window(window: tauri::Window) {
    window.maximize().ok();
}

#[tauri::command]
fn unmaximize_window(window: tauri::Window) {
    window.unmaximize().ok();
}

#[tauri::command]
fn show_window(window: tauri::Window) {
    window.show().ok();
}

#[tauri::command]
fn hide_window(window: tauri::Window) {
    window.hide().ok();
}

#[tauri::command]
fn set_window_title(window: tauri::Window, title: String) {
    window.set_title(&title).ok();
}

fn main() {
    tauri::Builder::default()
        .invoke_handler(tauri::generate_handler![
            create_window,
            close_window,
            minimize_window,
            maximize_window,
            unmaximize_window,
            show_window,
            hide_window,
            set_window_title
        ])
        .setup(|app| {
            let main_window = app.get_window("main").unwrap();
            
            // 设置默认窗口属性
            main_window.set_title("NativePHP App").ok();
            main_window.set_decorations(true).ok();
            main_window.set_resizable(true).ok();

            #[cfg(debug_assertions)]
            main_window.open_devtools();

            Ok(())
        })
        .run(tauri::generate_context!())
        .expect("error while running tauri application");
}