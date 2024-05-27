<?php
/**
 * Category service.
 */

namespace App\Service;

use App\Entity\Category;
use App\Entity\Task;
use App\Repository\CategoryRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class CategoryService.
 */
class CategoryService implements CategoryServiceInterface
{
    /**
     * Paginator.
     */
    private PaginatorInterface $paginator;

    /**
     * Category repository.
     */
    private CategoryRepository $categoryRepository;

    /**
     * Transaction repository.
     */
    private TaskRepository $taskRepository;

    /**
     * Constructor.
     *
     * @param CategoryRepository    $categoryRepository Category repository
     * @param PaginatorInterface    $paginator          Paginator
     * @param TaskRepository $taskRepository     Transaction repository
     */
    public function __construct(CategoryRepository $categoryRepository, PaginatorInterface $paginator, TaskRepository $taskRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->paginator = $paginator;
        $this->taskRepository = $taskRepository;
    }

    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->categoryRepository->queryAll(),
            $page,
            TaskRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save entity.
     *
     * @param Category $category Category entity
     */
    public function save(Category $category): void
    {
        $category->setCreatedAt(new \DateTimeImmutable());
        if (null !== $category->getId()) {
            $category->setUpdatedAt(new \DateTimeImmutable());
        }
        $this->categoryRepository->save($category);
    }

    /**
     * Delete entity.
     *
     * @param Category $category Category
     */
    public function delete(Category $category): void
    {
        $this->categoryRepository->delete($category);
    }

    /**
     * Can Category be deleted?
     *
     * @param Category $category Category entity
     *
     * @return bool Result
     */
    public function canBeDeleted(Category $category): bool
    {
        try {
            $result = $this->taskRepository->countByCategory($category);

            return !($result > 0);
        } catch (NoResultException|NonUniqueResultException) {
            return false;
        }
    }

    /**
     * Find by id.
     *
     * @param int $id Category id
     *
     * @return Category|null Category entity
     *
     * @throws NonUniqueResultException
     */
    public function findOneById(int $id): ?Category
    {
        return $this->categoryRepository->findOneById($id);
    }
}