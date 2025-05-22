<?php

namespace App\Entity;



class SearchFilters 
{
    private ?int $id = null;

    private ?object $categories = null;
    private ?string $string = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategories(): ?object
    {
        return $this->categories;
    }

    public function setCategories(object $categories): static
    {
        $this->categories = $categories;

        return $this;
    }
    
   

    /**
     * Get the value of string
     */ 
    public function getString()
    {
        return $this->string;
    }

    /**
     * Set the value of string
     *
     * @return  self
     */ 
    public function setString($string)
    {
        $this->string = $string;

        return $this;
    }
}
