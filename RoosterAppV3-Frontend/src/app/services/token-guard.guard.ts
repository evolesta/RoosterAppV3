import { CanActivateFn } from '@angular/router';

export const tokenGuardGuard: CanActivateFn = (route, state) => {
  return true;
};
