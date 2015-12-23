<?php

class HomeController extends BaseController {

    public function inicio() {
        $items_home = array();
        $destacados = array();

        $total_home = 12;

        $slideIndex = parent::slideIndex();
        $items_oferta = parent::itemsOferta($total_home);

        if (count($items_oferta) < $total_home) {
            foreach ($items_oferta as $item_of) {
                array_push($destacados, $item_of->id);
                array_push($items_home, $item_of);
            }

            $items_nuevos = parent::itemsNuevos($total_home - count($items_home));

            if ((count($items_home) + count($items_nuevos)) < $total_home) {

                if (count($items_nuevos) > 0) {

                    foreach ($items_nuevos as $item) {
                        array_push($destacados, $item->id);
                        array_push($items_home, $item);
                    }
                }
                $limit = $total_home - count($items_home);
                //echo $limit;
                $ultimos_productos = parent::ultimosProductos($destacados, $limit);

                foreach ($ultimos_productos as $item_ul) {
                    array_push($items_home, $item_ul);
                }
            } else {
                foreach ($items_nuevos as $item) {
                    array_push($items_home, $item);
            }
        }
        } else {
            $items_home = $items_oferta;
        }

        /*
          $items_nuevos = parent::itemsNuevos();

          if (count($items_nuevos) < 8) {
          if (count($items_nuevos) > 0) {
          $destacados = array();
          foreach ($items_nuevos as $item) {
          array_push($destacados, $item->id);
          }

          $ultimos_productos = Item::where('estado', 'A')->whereNotIn('id', $destacados)->orderBy('fecha_modificacion', 'desc')->skip(0)->take(8 - count($items_nuevos))->get();
          } else {
          $ultimos_productos = Item::where('estado', 'A')->orderBy('fecha_modificacion', 'desc')->skip(0)->take(8 - count($items_nuevos))->get();
          }
          } else {
          $ultimos_productos = NULL;
          }
         * 
         */

        //$this->array_view['items_nuevos'] = $items_nuevos;
        $this->array_view['slide_index'] = $slideIndex;
        //$this->array_view['ultimos_productos'] = $ultimos_productos;
        $this->array_view['items_home'] = $items_home;

        return View::make($this->project_name . '-inicio', $this->array_view);
    }

    public function contacto() {

        return View::make($this->project_name . '-contacto', $this->array_view);
    }

    public function error() {

        $texto = Session::get('texto');

        $this->array_view['texto'] = $texto;

        return Redirect::to('/');
        //return View::make($this->project_name . '-error', $this->array_view);
    }

    public function consultaContacto() {

        $data = Input::all();
        $this->array_view['data'] = $data;

        Mail::send('emails.consulta-contacto', $this->array_view, function($message) use($data) {
            $message->from($data['email'], $data['nombre'])
                    ->to('max.-ang@hotmail.com.ar')
                    ->subject('Consulta')
            ;
        });

        if (count(Mail::failures()) > 0) {
            $mensaje = 'El mail no pudo enviarse.';
        } else {
            $mensaje = 'El mail se envió correctamente';
        }

        if (isset($data['continue']) && ($data['continue'] != "")) {
            switch ($data['continue']) {
                case "contacto":
                    return Redirect::to('contacto')->with('mensaje', $mensaje);
                    break;
                case "menu":
                    $menu = Menu::find($data['menu_id']);

                    return Redirect::to('/' . $menu->url)->with('mensaje', $mensaje);
                    break;
            }
        }

        return Redirect::to("/")->with('mensaje', $mensaje);
        //return View::make('producto.editar', $this->array_view);
    }

    public function pasarItemsIdioma() {

        $idiomas = Idioma::where('estado', 'A')->get();



        $items_sin_idioma = Item::where('estado', 'A')->whereNotIn('id', function($q) {
                            $q->select('item_id')
                            ->from('item_lang')
                            ->where('estado', 'A');
                        })
                        ->orderBy('id', 'ASC')->get();

        foreach ($items_sin_idioma as $item_sin) {
            echo "ID: " . $item_sin->id . "<br>";
            echo "Titulo: " . $item_sin->titulo . "<br>";
            echo "Descripcion: " . $item_sin->descripcion . "<br>";
            echo "URL: " . $item_sin->url . "<br><br>";
            
            $datos_lang = array(
                'titulo' => $item_sin->titulo,
                'descripcion' => $item_sin->descripcion,
                'url' => $item_sin->url,
                'estado' => 'A',
                'fecha_carga' => date("Y-m-d H:i:s"),
                'usuario_id_carga' => 1
            );

            foreach ($idiomas as $idioma) {
                /*
                  if ($idioma->codigo != Config::get('app.locale')) {
                  $datos_lang['url'] = $idioma->codigo . "/" . $datos_lang['url'];
}
                 * 
                 */
                $item_sin->idiomas()->attach($idioma->id, $datos_lang);
            }
        }

        $imagenes_sin_idioma = Imagen::where('estado', 'A')->whereNotIn('id', function($q) {
                            $q->select('imagen_id')
                            ->from('imagen_lang')
                            ->where('estado', 'A');
                        })
                        ->orderBy('id', 'ASC')->get();
        /*
          echo "CANTIDAD IMG: " . count($imagenes_sin_idioma);
          foreach ($imagenes_sin_idioma as $img_sin) {
          $datos_lang = array(
          'epigrafe' => $img_sin->epigrafe,
          'estado' => 'A',
          'fecha_carga' => date("Y-m-d H:i:s"),
          'usuario_id_carga' => 1
          );

          echo "ID IMG: " . $img_sin->id . "<br>";

          foreach ($idiomas as $idioma) {
          echo "ID IDIOMA: " . $idioma->id . "<br>";
          $img_sin->idiomas()->attach($idioma->id, $datos_lang);
          echo "PASO <br>";
          }
          }

          echo "LISTO";
         * 
         */

        $productos_sin_idioma = Producto::whereIn('item_id', function($p) {
                            $p->select('id')
                            ->from('item')
                            ->where('estado', 'A');
                        })->whereNotIn('id', function($q) {
                            $q->select('producto_id')
                            ->from('producto_lang');
                        })
                        ->orderBy('id', 'ASC')->get();

        echo "CANT PROD: " . count($productos_sin_idioma);
        foreach ($productos_sin_idioma as $prod_sin) {
            echo "ID: " . $prod_sin->id . "<br>";
            echo "Cuerpo: " . $prod_sin->cuerpo . "<br><br>";

            $datos_lang = array(
                'cuerpo' => $prod_sin->cuerpo,
            );

            foreach ($idiomas as $idioma) {
                $prod_sin->idiomas()->attach($idioma->id, $datos_lang);
            }
        }
    }

}
