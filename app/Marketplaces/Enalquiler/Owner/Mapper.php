<?php namespace App\Marketplaces\Enalquiler\Owner;

class Mapper {

    protected $item;

    protected $errors = [];

    public function __construct(array $item)
    {
        $this->item = $item;
    }

    /**
     * Maps a Molista owner to enalquiler.com format according to:
     * http://www.enalquiler.com/feeds/public/usuarios.xsd
     * http://www.enalquiler.com/feeds/public/usuarios.xml
     *
     * @return array
     */
    public function map()
    {
        $item = $this->item;

        $map = [];
        $map['id'] = $item['id'];
        $map['email'] = $item['email'];
        $map['nombre_agencia'] = $item['fullname'];
        $map['cif'] = $item['cif'];

        return $map;
    }

    public function valid()
    {
        $validator = \Validator::make($this->item, [
            'id' => 'required',
            'email' => 'required|email',
            'fullname' => 'required',
            'cif' => ['required', 'regex:#(^[A|B|C|D|E|F|G|H|J|K|L|M|N|P|Q|S|V][0-9]{7}[0-9A-J]$)|(^[X|Y|Z][0-9]{7}[T|R|W|A|G|M|Y|F|P|D|X|B|N|J|Z|S|Q|V|H|L|C|K|E]$)|(^[0-9]{8}[T|R|W|A|G|M|Y|F|P|D|X|B|N|J|Z|S|Q|V|H|L|C|K|E]$)#']
        ]);
        if ($validator->fails())
        {
            return $validator->errors()->all();
        }

        return true;
    }

    public function errors()
    {
        return $this->errors;
    }

}
