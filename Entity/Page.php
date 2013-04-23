<?php
namespace Silverkix\CMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="pages")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Page
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @ORM\Column(type="string")
     */
    private $slug;

    /**
     * @ORM\Column(type="string")
     */
    private $keywords;

    /**
     * @ORM\Column(type="string")
     */
    private $description;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

    // ...
    /**
     * @ORM\OneToMany(targetEntity="Page", mappedBy="parent")
     **/
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="Page", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     **/
    private $parent;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $online;

    /**
     * @ORM\Column(type="integer")
     */
    private $orderid;

    public function __construct() {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Page
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set keywords
     *
     * @param string $keywords
     * @return Page
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * Get keywords
     *
     * @return string
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Page
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Page
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set online
     *
     * @param boolean $online
     * @return Page
     */
    public function setOnline($online)
    {
        $this->online = $online;

        return $this;
    }

    /**
     * Get online
     *
     * @return boolean
     */
    public function getOnline()
    {
        return $this->online;
    }

    /**
     * Clean name
     *
     * @return string
     */
    public function CleanName($Raw){
        $clean = str_replace("'", '', $Raw);
        $clean = str_replace('-', ' ', $clean);
        $clean = preg_replace('~[^\\pL0-9_]+~u', '-', $clean); // substitutes anything but letters, numbers and '_' with separator
        $clean = trim($clean, "-");
        $clean = iconv("utf-8", "us-ascii//TRANSLIT", $clean);  // you may opt for your own custom character map for encoding.
        $clean = strtolower($clean);
        $clean = preg_replace('~[^-a-z0-9_]+~', '', $clean); // keep only letters, numbers, '_' and separator
        return $clean;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Page
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Add children
     *
     * @param \Silverkix\CMSBundle\Entity\Page $children
     * @return Page
     */
    public function addChildren(\Silverkix\CMSBundle\Entity\Page $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \Silverkix\CMSBundle\Entity\Page $children
     */
    public function removeChildren(\Silverkix\CMSBundle\Entity\Page $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set parent
     *
     * @param \Silverkix\CMSBundle\Entity\Page $parent
     * @return Page
     */
    public function setParent(\Silverkix\CMSBundle\Entity\Page $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Silverkix\CMSBundle\Entity\Page
     */
    public function getParent()
    {
        return $this->parent;
    }

    public function __toString()
    {
        return $this->getTitle();
    }

    /**
     * @ORM\PreUpdate
     * @ORM\PrePersist
     */
    public function updateSlug()
    {
        // Only update if the slug is not emtpy
        if($this->getSlug() !== '')
        {
            if($this->getParent() !== null)
            {
                $this->setSlug( $this->getParent()->getSlug()."/".$this->CleanName( $this->getTitle() ) );
            }
            else
            {
                $this->setSlug( $this->CleanName( $this->getTitle() ) );
            }
        }
    }

    /**
     * Set orderid
     *
     * @param integer $orderid
     * @return Page
     */
    public function setOrderid($orderid)
    {
        $this->orderid = $orderid;

        return $this;
    }

    /**
     * Get orderid
     *
     * @return integer
     */
    public function getOrderid()
    {
        return $this->orderid;
    }
}
