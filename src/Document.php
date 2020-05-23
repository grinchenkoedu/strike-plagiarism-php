<?php

namespace Matasar\StrikePlagiarism;

class Document
{
    use DataTrait;
    use ValidationTrait;

    /**
     * Antiplagiarism analysis
     */
    const ACTION_CHECK = 'check';

    /**
     * Adding to reference database only
     */
    const ACTION_INDEX = 'index';

    const SUPPORTED_ACTIONS = [
        self::ACTION_CHECK,
        self::ACTION_INDEX
    ];

    const KIND_HABILITATION_THESIS = 0;
    const KIND_DOCTORAL_THESIS = 1;
    const KIND_MASTERS_THESIS = 2;
    const KIND_LICENTIATE = 3;
    const KIND_YEAR_PAPER = 4;
    const KIND_ESSAY = 6;
    const KIND_ARTICLE = 7;
    const KIND_CASE_STUDY = 8;
    const KIND_ENGINEER= 9;
    const KIND_DIPLOMA_PAPER = 10;
    const KIND_PROJECT = 16;
    const KIND_OTHER = 29;
    const KIND_POSTGRADUATE_THESIS = 40;
    const KIND_MONOGRAPH = 101;
    const KIND_TREATISE = 103;
    const KIND_BOOK = 300;

    const SUPPORTED_KINDS = [
        self::KIND_HABILITATION_THESIS,
        self::KIND_DOCTORAL_THESIS,
        self::KIND_MASTERS_THESIS,
        self::KIND_LICENTIATE,
        self::KIND_YEAR_PAPER,
        self::KIND_ESSAY,
        self::KIND_ARTICLE,
        self::KIND_CASE_STUDY,
        self::KIND_ENGINEER,
        self::KIND_DIPLOMA_PAPER,
        self::KIND_PROJECT,
        self::KIND_OTHER,
        self::KIND_POSTGRADUATE_THESIS,
        self::KIND_MONOGRAPH,
        self::KIND_TREATISE,
        self::KIND_BOOK
    ];

    /**
     * @var string
     */
    protected $languageCode;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $author;

    /**
     * @var string
     */
    protected $coordinator;

    /**
     * @var string
     */
    protected $fileUri;

    /**
     * @var string|null
     */
    protected $faculty;

    /**
     * @var string
     */
    protected $action = self::ACTION_CHECK;

    /**
     * @var string|null
     */
    protected $id;

    /**
     * @var string|null
     */
    protected $reviewer;

    /**
     * @var int
     */
    protected $documentKind = self::KIND_MASTERS_THESIS;

    /**
     * @param string $languageCode
     * @param string $title
     * @param string $author
     * @param string $coordinator
     * @param string $fileUri
     */
    public function __construct(
        string $languageCode,
        string $title,
        string $author,
        string $coordinator,
        string $fileUri
    ) {
        $this->setLanguageCode($languageCode);
        $this->setTitle($title);
        $this->setAuthor($author);
        $this->setCoordinator($coordinator);
        $this->setFileUri($fileUri);
    }

    /**
     * @param string $languageCode
     */
    public function setLanguageCode(string $languageCode): void
    {
        $this->languageCode = $this->validateLanguageCode($languageCode);
    }

    /**
     * @return string
     */
    public function getLanguageCode(): string
    {
        return $this->languageCode;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $author
     */
    public function setAuthor(string $author): void
    {
        $this->author = $author;
    }

    /**
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * @param string $coordinator
     */
    public function setCoordinator(string $coordinator): void
    {
        $this->coordinator = $coordinator;
    }

    /**
     * @return string
     */
    public function getCoordinator(): string
    {
        return $this->coordinator;
    }

    /**
     * @param string $fileUri
     */
    public function setFileUri(string $fileUri): void
    {
        $this->fileUri = $fileUri;
    }

    /**
     * @return string
     */
    public function getFileUri(): string
    {
        return $this->fileUri;
    }

    /**
     * @param string|null $faculty
     */
    public function setFaculty(?string $faculty): void
    {
        $this->faculty = $faculty;
    }

    /**
     * @return string|null
     */
    public function getFaculty(): ?string
    {
        return $this->faculty;
    }

    /**
     * @param string $action
     */
    public function setAction(string $action): void
    {
        if (!in_array($action, self::SUPPORTED_ACTIONS)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Wrong action provided, supported: %s',
                    implode(', ', self::SUPPORTED_ACTIONS)
                )
            );
        }

        $this->action = $action;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @param string|null $id
     */
    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string|null $reviewer
     */
    public function setReviewer(?string $reviewer): void
    {
        $this->reviewer = $reviewer;
    }

    /**
     * @return string|null
     */
    public function getReviewer(): ?string
    {
        return $this->reviewer;
    }

    /**
     * @param int $documentKind
     */
    public function setDocumentKind(int $documentKind): void
    {
        $this->documentKind = $documentKind;
    }

    /**
     * @return int
     */
    public function getDocumentKind(): int
    {
        return $this->documentKind;
    }
}
